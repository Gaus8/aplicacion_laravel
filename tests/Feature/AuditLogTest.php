<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_audit_log(): void
    {
        $this->get(route('admin.audit.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_filter_audit_entries(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        app(AuditLogger::class)->record('smtp.settings.updated', 'success', $user, 'Configuración SMTP');
        app(AuditLogger::class)->record('auth.login.failed', 'failure', null, 'Intento de inicio de sesión');

        $this->get(route('admin.audit.index', ['search' => 'Configuración SMTP']))
            ->assertOk()
            ->assertSeeText('Registro de auditoría')
            ->assertSeeText('Configuración SMTP actualizada')
            ->assertDontSeeText('Intento de inicio de sesión fallido');

        $this->get(route('admin.audit.index', ['status' => 'failure']))
            ->assertOk()
            ->assertSeeText('Intento de inicio de sesión fallido')
            ->assertDontSeeText('Configuración SMTP actualizada');
    }

    public function test_successful_login_and_logout_are_recorded(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('password-for-test'),
        ]);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password-for-test'])
            ->assertRedirect('/admin/dashboard');
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $user->id,
            'event' => 'auth.login.succeeded',
            'status' => 'success',
        ]);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $user->id,
            'event' => 'auth.logout',
            'status' => 'success',
        ]);
    }

    public function test_failed_login_is_recorded_without_storing_password_or_email(): void
    {
        $this->post(route('login'), ['email' => 'unknown@example.test', 'password' => 'do-not-store-this'])
            ->assertRedirect();

        $entry = AuditLog::query()->where('event', 'auth.login.failed')->firstOrFail();
        $this->assertNull($entry->actor_user_id);
        $this->assertNull($entry->metadata['password'] ?? null);
        $this->assertNotSame('unknown@example.test', $entry->subject);
        $this->assertSame('failure', $entry->status);
    }

    public function test_audit_metadata_only_keeps_the_safe_allowlist(): void
    {
        $entry = app(AuditLogger::class)->record('smtp.settings.updated', 'success', null, 'SMTP', [
            'smtp_active' => true,
            'smtp_authentication_required' => false,
            'password' => 'must-not-be-recorded',
            'host' => 'private.example.test',
        ]);

        $this->assertSame([
            'smtp_active' => true,
            'smtp_authentication_required' => false,
        ], $entry->fresh()->metadata);
    }
}

<?php

namespace Tests\Feature;

use App\Models\SmtpSetting;
use App\Models\User;
use App\Services\SmtpMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Mockery;
use Tests\TestCase;

class SmtpSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_smtp_settings(): void
    {
        $this->get(route('admin.smtp.edit'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_smtp_settings(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.smtp.edit'))
            ->assertOk()
            ->assertSeeText('Configuración de correo')
            ->assertSeeText('Multimedia');
    }

    public function test_authenticated_user_can_save_encrypted_smtp_credentials(): void
    {
        $this->actingAs(User::factory()->create());

        $this->put(route('admin.smtp.update'), $this->validSettings())
            ->assertRedirect(route('admin.smtp.edit'));

        $setting = SmtpSetting::query()->firstOrFail();
        $this->assertNotSame('smtp-secret', $setting->password_encrypted);
        $this->assertSame('smtp-secret', Crypt::decryptString($setting->password_encrypted));
        $this->assertTrue($setting->is_active);
    }

    public function test_saving_without_a_new_password_preserves_the_encrypted_credential(): void
    {
        $this->actingAs(User::factory()->create());
        $data = $this->validSettings();
        $this->put(route('admin.smtp.update'), $data);
        $encrypted = SmtpSetting::query()->firstOrFail()->password_encrypted;

        $data['password'] = '';
        $data['from_name'] = 'CMS actualizado';
        $this->put(route('admin.smtp.update'), $data)->assertRedirect(route('admin.smtp.edit'));

        $setting = SmtpSetting::query()->firstOrFail();
        $this->assertSame($encrypted, $setting->password_encrypted);
        $this->assertSame('CMS actualizado', $setting->from_name);
    }

    public function test_test_email_uses_the_authenticated_users_address(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $setting = SmtpSetting::create([
            ...$this->validSettings(),
            'password_encrypted' => Crypt::encryptString('smtp-secret'),
        ]);

        $mailer = Mockery::mock(SmtpMailer::class);
        $mailer->shouldReceive('test')->once()->withArgs(
            fn (SmtpSetting $actual, string $recipient): bool => $actual->is($setting) && $recipient === $user->email
        );
        $this->app->instance(SmtpMailer::class, $mailer);

        $this->post(route('admin.smtp.test'))
            ->assertRedirect(route('admin.smtp.edit'))
            ->assertSessionHas('success');

        $this->assertSame('success', $setting->fresh()->last_test_status);
        $this->assertNotNull($setting->fresh()->last_tested_at);
    }

    private function validSettings(): array
    {
        return [
            'host' => 'smtp.example.test',
            'port' => 587,
            'encryption' => 'tls',
            'authentication_required' => 1,
            'username' => 'cms@example.test',
            'password' => 'smtp-secret',
            'from_address' => 'noreply@example.test',
            'from_name' => 'CMS',
            'is_active' => 1,
        ];
    }
}

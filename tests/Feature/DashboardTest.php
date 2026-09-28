<?php

namespace Tests\Feature;

use App\Models\EmailSent;
use App\Models\Media;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_real_database_counts_and_recent_records(): void
    {
        $owner = User::factory()->create(['name' => 'Responsable CMS']);
        User::factory()->count(2)->create();
        $this->actingAs($owner);

        Media::create(['name' => 'Logo reciente', 'path' => 'media/logo.png', 'mime_type' => 'image/png', 'size' => 100]);
        Media::create(['name' => 'Portada reciente', 'path' => 'media/portada.jpg', 'mime_type' => 'image/jpeg', 'size' => 200]);

        EmailSent::create([
            'user_id' => $owner->id,
            'recipient' => 'destino@example.test',
            'subject' => 'Resumen mensual real',
            'message' => 'Contenido privado del mensaje',
        ]);
        $olderEmail = EmailSent::create([
            'user_id' => $owner->id,
            'recipient' => 'archivo@example.test',
            'subject' => 'Correo anterior',
            'message' => 'Contenido antiguo',
        ]);
        $olderEmail->created_at = now()->subMonths(2);
        $olderEmail->save();

        app(AuditLogger::class)->record('auth.login.failed', 'failure', null, 'Intento de inicio de sesión');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Hola, Responsable CMS')
            ->assertSeeText('Auditoría')
            ->assertSeeText('Cuentas registradas')
            ->assertSeeText('Archivos multimedia')
            ->assertSeeText('2 cargados en los últimos 7 días')
            ->assertSeeText('Correos enviados este mes')
            ->assertSeeText('1 enviados hoy')
            ->assertSeeText('Accesos fallidos hoy')
            ->assertSeeText('Actividad auditada')
            ->assertSeeText('Logo reciente')
            ->assertSeeText('Portada reciente')
            ->assertSeeText('Resumen mensual real')
            ->assertDontSeeText('Contenido privado del mensaje')
            ->assertDontSeeText('Visitas hoy')
            ->assertDontSeeText('WAF');
    }

    public function test_dashboard_handles_empty_tables(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Sin actividad registrada')
            ->assertSeeText('No hay archivos todavía')
            ->assertSeeText('No hay correos registrados');
    }

    public function test_dashboard_preserves_authenticated_json_response(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson(route('dashboard'))
            ->assertOk()
            ->assertJsonPath('message', 'Usuario Autenticado. Bienvenido al Dashboard')
            ->assertJsonPath('user.id', $user->id);
    }
}

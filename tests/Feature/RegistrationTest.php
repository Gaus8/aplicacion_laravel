<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_links_to_the_registration_panel_and_panel_has_required_fields(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('registro'))
            ->assertSeeText('Crear una cuenta');

        $this->get(route('registro'))
            ->assertOk()
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_guest_can_register_with_a_confirmed_password_without_admin_permissions(): void
    {
        $payload = [
            '_token' => 'registration-test-token',
            'name' => 'Usuario nuevo',
            'email' => 'nuevo@example.test',
            'password' => 'secure-password-123',
            'password_confirmation' => 'secure-password-123',
        ];

        $this->withSession(['_token' => 'registration-test-token'])
            ->post(route('registro.store'), $payload)
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Registro exitoso. Inicia sesión.');

        $user = User::query()->where('email', 'nuevo@example.test')->firstOrFail();

        $this->assertTrue(Hash::check('secure-password-123', $user->password));
    }

    public function test_password_confirmation_is_required_to_register(): void
    {
        $payload = [
            '_token' => 'registration-test-token',
            'name' => 'Usuario nuevo',
            'email' => 'nuevo@example.test',
            'password' => 'secure-password-123',
            'password_confirmation' => 'different-password-123',
        ];

        $this->withSession(['_token' => 'registration-test-token'])
            ->from(route('registro'))
            ->post(route('registro.store'), $payload)
            ->assertRedirect(route('registro'))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'nuevo@example.test']);
    }
}

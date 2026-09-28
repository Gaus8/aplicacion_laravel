<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_crud_assigns_roles_and_hashes_passwords(): void
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Editor', 'slug' => 'editor']);
        $payload = ['name' => 'Editor User', 'email' => 'editor@example.test', 'password' => 'strong-password-123', 'password_confirmation' => 'strong-password-123', 'is_active' => '1', 'roles' => [$role->id]];
        $this->actingAs($admin)->post(route('admin.users.store'), $payload)->assertRedirect(route('admin.users.index'));
        $user = User::where('email', 'editor@example.test')->firstOrFail();
        $this->assertTrue(password_verify('strong-password-123', $user->password));
        $this->assertTrue($user->roles()->whereKey($role->id)->exists());
        $this->actingAs($admin)->put(route('admin.users.update', $user), array_replace($payload, ['name' => 'Editora', 'password' => '', 'password_confirmation' => '', 'is_active' => '0']))->assertRedirect(route('admin.users.index'));
        $this->assertFalse($user->fresh()->is_active);
    }

    public function test_inactive_user_cannot_log_in_and_guests_cannot_manage_users(): void
    {
        $inactive = User::factory()->create(['email' => 'inactive@example.test', 'password' => bcrypt('password') , 'is_active' => false]);
        $this->post(route('login'), ['email' => $inactive->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    }

    public function test_last_administrator_cannot_remove_its_own_access(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name, 'email' => $admin->email, 'password' => '', 'password_confirmation' => '',
            'is_active' => '1', 'roles' => [],
        ])->assertStatus(422);
        $this->assertTrue($admin->fresh()->hasPermission('roles.manage'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesPermissionsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_permissions_control_admin_route_access(): void
    {
        $admin = User::factory()->create();
        $permission = Permission::where('name', 'dashboard.view')->firstOrFail();
        $role = Role::create(['name' => 'Solo dashboard', 'slug' => 'solo-dashboard']);
        $role->permissions()->sync([$permission->id]);
        $user = User::factory()->create();
        $user->roles()->sync([$role->id]);

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
        $this->get(route('admin.banners.index'))->assertForbidden();
        $token = $user->createToken('permissions-test')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/dashboard')->assertOk();
        $this->actingAs($admin)->post(route('admin.roles.store'), ['name' => 'Editor', 'description' => 'Edición de contenido', 'permissions' => ['posts.manage']])->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['slug' => 'editor']);
    }

    public function test_system_administrator_role_cannot_be_edited_or_deleted(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::where('slug', 'administrador')->firstOrFail();
        $this->actingAs($user)->get(route('admin.roles.edit', $adminRole))->assertForbidden();
        $this->delete(route('admin.roles.destroy', $adminRole))->assertForbidden();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use App\Support\UniqueSlug;

class RoleController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Role::class);
        $roles = Role::query()->withCount('users')->with('permissions')->orderBy('name')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        Gate::authorize('create', Role::class);
        return view('admin.roles.form', ['role' => new Role, 'permissions' => Permission::query()->orderBy('group')->orderBy('label')->get()->groupBy('group'), 'editing' => false]);
    }

    public function store(StoreRoleRequest $request, UniqueSlug $uniqueSlug)
    {
        $data = $request->validated();
        $role = Role::create(['name' => trim($data['name']), 'slug' => $uniqueSlug->make(Role::class, $data['name']), 'description' => $data['description'] ?? null]);
        $role->permissions()->sync(Permission::query()->whereIn('name', $data['permissions'] ?? [])->pluck('id'));
        return redirect()->route('admin.roles.index')->with('success', 'El rol fue creado.');
    }

    public function edit(Role $role)
    {
        Gate::authorize('update', $role);
        return view('admin.roles.form', ['role' => $role->load('permissions'), 'permissions' => Permission::query()->orderBy('group')->orderBy('label')->get()->groupBy('group'), 'editing' => true]);
    }

    public function update(UpdateRoleRequest $request, Role $role, UniqueSlug $uniqueSlug)
    {
        Gate::authorize('update', $role);
        $data = $request->validated();
        $role->update(['name' => trim($data['name']), 'slug' => $uniqueSlug->make(Role::class, $data['name'], $role->id), 'description' => $data['description'] ?? null]);
        $role->permissions()->sync(Permission::query()->whereIn('name', $data['permissions'] ?? [])->pluck('id'));
        return redirect()->route('admin.roles.index')->with('success', 'El rol fue actualizado.');
    }

    public function destroy(Role $role)
    {
        Gate::authorize('delete', $role);
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'El rol fue eliminado.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', User::class);
        $users = User::query()->with('roles')->when(request('q'), fn ($query, $term) => $query->where(fn ($nested) => $nested->where('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')))->latest()->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        Gate::authorize('create', User::class);
        return view('admin.users.form', ['user' => new User(['is_active' => true]), 'roles' => Role::query()->orderBy('name')->get(), 'editing' => false]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $user = User::create(['name' => trim($data['name']), 'email' => mb_strtolower($data['email']), 'password' => Hash::make($data['password']), 'is_active' => (bool) $data['is_active']]);
        $user->roles()->sync($data['roles'] ?? []);
        return redirect()->route('admin.users.index')->with('success', 'El usuario fue creado.');
    }

    public function edit(User $user)
    {
        Gate::authorize('update', $user);
        return view('admin.users.form', ['user' => $user->load('roles'), 'roles' => Role::query()->orderBy('name')->get(), 'editing' => true]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);
        $data = $request->validated();
        $attributes = ['name' => trim($data['name']), 'email' => mb_strtolower($data['email']), 'is_active' => (bool) $data['is_active']];
        if (!empty($data['password'])) $attributes['password'] = Hash::make($data['password']);
        $administrator = Role::query()->where('slug', 'administrador')->first();
        $hadRoleManagement = $user->hasPermission('roles.manage');
        $newRoles = $data['roles'] ?? [];
        if ($user->is(auth()->user()) && !$attributes['is_active']) abort(422, 'No puedes desactivar tu propia cuenta.');
        if ($administrator && $user->roles()->whereKey($administrator->id)->exists() && !in_array($administrator->id, $newRoles, true) && $administrator->users()->count() <= 1) {
            abort(422, 'Debe permanecer al menos una cuenta administradora.');
        }
        DB::transaction(function () use ($user, $attributes, $newRoles, $administrator, $hadRoleManagement): void {
            $user->update($attributes);
            $user->roles()->sync($newRoles);
            if ($user->is(auth()->user()) && !$user->hasPermission('users.manage')) abort(422, 'La cuenta debe conservar acceso a la gestión de usuarios.');
            if ($user->is(auth()->user()) && $hadRoleManagement && !$user->hasPermission('roles.manage')) abort(422, 'La cuenta debe conservar acceso a la gestión de roles.');
            if (!$user->is_active) $user->tokens()->delete();
            if ($administrator && $administrator->users()->where('is_active', true)->doesntExist()) abort(422, 'Debe permanecer al menos una cuenta administradora activa.');
            $roleManagerExists = User::query()->where('is_active', true)->whereHas('roles.permissions', fn ($query) => $query->where('permissions.name', 'roles.manage'))->exists();
            if (!$roleManagerExists) abort(422, 'Debe permanecer al menos un administrador de roles activo.');
        });
        return redirect()->route('admin.users.index')->with('success', 'El usuario fue actualizado.');
    }

    public function destroy(User $user)
    {
        Gate::authorize('update', $user);
        abort_if($user->is(auth()->user()), 422, 'No puedes eliminar tu propia cuenta.');
        $administrator = Role::query()->where('slug', 'administrador')->first();
        abort_if($administrator && $user->roles()->whereKey($administrator->id)->exists() && $administrator->users()->count() <= 1, 422, 'Debe permanecer al menos una cuenta administradora.');
        abort_if($user->hasPermission('roles.manage') && User::query()->where('is_active', true)->whereHas('roles.permissions', fn ($query) => $query->where('permissions.name', 'roles.manage'))->count() <= 1, 422, 'Debe permanecer al menos un administrador de roles activo.');
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'El usuario fue eliminado.');
    }
}

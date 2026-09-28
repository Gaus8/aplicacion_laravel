@extends('layouts.admin')
@section('title', 'Roles y permisos')
@section('content')
<div class="space-y-6"><div class="flex flex-wrap items-center justify-between gap-4"><div><h1 class="font-display text-2xl font-semibold">Roles y permisos</h1><p class="text-sm text-slate-600">Controla el acceso administrativo por responsabilidad.</p></div><a href="{{ route('admin.roles.create') }}" class="inline-flex items-center rounded-md bg-secondary px-4 py-2 text-sm font-medium text-white">Crear rol</a></div>
    @if(session('success'))<x-alert type="success">{{ session('success') }}</x-alert>@endif
    <x-card><div class="overflow-x-auto"><x-table><thead><tr><th>Rol</th><th>Permisos</th><th>Usuarios</th><th></th></tr></thead><tbody>@forelse($roles as $role)<tr><td><span class="font-medium">{{ $role->name }}</span>@if($role->is_system)<x-badge variant="info">Sistema</x-badge>@endif<p class="text-xs text-slate-500">{{ $role->description }}</p></td><td>{{ $role->permissions->count() }}</td><td>{{ $role->users_count }}</td><td>@if(!$role->is_system)<a href="{{ route('admin.roles.edit', $role) }}" class="text-secondary hover:underline">Editar</a>@endif</td></tr>@empty<tr><td colspan="4" class="py-8 text-center text-slate-500">No hay roles.</td></tr>@endforelse</tbody></x-table></div>{{ $roles->links() }}</x-card>
</div>
@endsection

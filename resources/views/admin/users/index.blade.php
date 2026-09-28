@extends('layouts.admin')
@section('title', 'Usuarios')
@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4"><div><h1 class="font-display text-2xl font-semibold">Usuarios</h1><p class="text-sm text-slate-600">Cuentas administrativas, roles y estado de acceso.</p></div><a href="{{ route('admin.users.create') }}" class="inline-flex items-center rounded-md bg-secondary px-4 py-2 text-sm font-medium text-white">Crear usuario</a></div>
    @if(session('success'))<x-alert type="success">{{ session('success') }}</x-alert>@endif
    <x-card><form method="GET" class="mb-4 flex gap-2"><x-input name="q" label="Buscar" :value="request('q')" placeholder="Nombre o correo"/><x-button type="submit" variant="secondary" class="mt-6">Buscar</x-button></form>
        <div class="overflow-x-auto"><x-table><thead><tr><th>Nombre</th><th>Correo</th><th>Roles</th><th>Estado</th><th></th></tr></thead><tbody>
        @forelse($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</td><td><x-badge :variant="$user->is_active ? 'success' : 'danger'">{{ $user->is_active ? 'Activo' : 'Desactivado' }}</x-badge></td><td><a class="text-secondary hover:underline" href="{{ route('admin.users.edit', $user) }}">Editar</a></td></tr>@empty<tr><td colspan="5" class="py-8 text-center text-slate-500">No hay usuarios.</td></tr>@endforelse
        </tbody></x-table></div>{{ $users->links() }}
    </x-card>
</div>
@endsection

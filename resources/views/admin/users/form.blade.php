@extends('layouts.admin')
@section('title', $editing ? 'Editar usuario' : 'Crear usuario')
@section('content')
<div class="mx-auto max-w-3xl space-y-6"><div><h1 class="font-display text-2xl font-semibold">{{ $editing ? 'Editar usuario' : 'Crear usuario' }}</h1></div>
    <x-card><form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="space-y-5">@csrf @if($editing)@method('PUT')@endif
        <x-input name="name" label="Nombre" :value="old('name', $user->name)" required />
        <x-input name="email" type="email" label="Correo" :value="old('email', $user->email)" required />
        <x-input name="password" type="password" label="{{ $editing ? 'Nueva contraseña (opcional)' : 'Contraseña (mínimo 12 caracteres)' }}" autocomplete="new-password" :required="!$editing" />
        <x-input name="password_confirmation" type="password" label="Confirmar contraseña" autocomplete="new-password" :required="!$editing" />
        <x-select name="is_active" label="Estado"><option value="1" @selected((string) old('is_active', (int) $user->is_active) === '1')>Activo</option><option value="0" @selected((string) old('is_active', (int) $user->is_active) === '0')>Desactivado</option></x-select>
        <fieldset class="space-y-2"><legend class="text-sm font-medium">Roles</legend>@foreach($roles as $role)<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked(collect(old('roles', $user->roles->modelKeys()))->contains($role->id))>{{ $role->name }}</label>@endforeach</fieldset>
        <div class="flex justify-end gap-3"><a href="{{ route('admin.users.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm">Cancelar</a><x-button type="submit">Guardar</x-button></div>
    </form></x-card>
</div>
@endsection

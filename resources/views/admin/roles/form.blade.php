@extends('layouts.admin')
@section('title', $editing ? 'Editar rol' : 'Crear rol')
@section('content')
<div class="mx-auto max-w-4xl space-y-6"><div><h1 class="font-display text-2xl font-semibold">{{ $editing ? 'Editar rol' : 'Crear rol' }}</h1></div>
    <x-card><form method="POST" action="{{ $editing ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="space-y-5">@csrf @if($editing)@method('PUT')@endif
        <x-input name="name" label="Nombre del rol" :value="old('name', $role->name)" required />
        <x-textarea name="description" label="Descripción" rows="2">{{ old('description', $role->description) }}</x-textarea>
        <fieldset class="space-y-5"><legend class="font-medium">Permisos</legend>@php($selectedPermissions = collect(old('permissions', $role->permissions->pluck('name')->all())))
            @foreach($permissions as $group => $groupPermissions)<div><h2 class="mb-2 text-sm font-semibold text-slate-700">{{ $group }}</h2><div class="grid gap-2 sm:grid-cols-2">@foreach($groupPermissions as $permission)<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked($selectedPermissions->contains($permission->name))>{{ $permission->label }}</label>@endforeach</div></div>@endforeach
        </fieldset>
        <div class="flex justify-end gap-3"><a href="{{ route('admin.roles.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm">Cancelar</a><x-button type="submit">Guardar rol</x-button></div>
    </form></x-card>
</div>
@endsection

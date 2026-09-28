@extends('layouts.admin')

@section('title', 'Servicios')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <x-breadcrumb :items="[['label' => 'Contenido'], ['label' => 'Servicios']]" />
                <h1 class="mt-3 font-display text-headline-xl-mobile font-bold text-slate-900 sm:text-headline-xl">Servicios</h1>
                <p class="mt-2 text-sm text-slate-600">Administra los servicios que se muestran en el sitio público.</p>
            </div>
            <a href="{{ route('admin.services.create') }}"><x-button><span aria-hidden="true">＋</span> Nuevo servicio</x-button></a>
        </div>

        @if(session('success'))<x-alert variant="success">{{ session('success') }}</x-alert>@endif

        <x-card>
            <form action="{{ route('admin.services.index') }}" method="GET" class="grid gap-3 sm:grid-cols-[1fr_12rem_auto] sm:items-end">
                <x-input name="search" label="Buscar" placeholder="Título o resumen" :value="$filters['search'] ?? ''" />
                <x-select name="status" label="Estado">
                    <option value="">Todos</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Activos</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactivos</option>
                </x-select>
                <x-button type="submit" variant="secondary">Filtrar</x-button>
            </form>
        </x-card>

        <x-card padding="none" class="overflow-hidden">
            @if($services->isEmpty())
                <div class="p-8"><x-empty-state title="Todavía no hay servicios" description="Crea un servicio para mostrar la oferta de tu organización en el sitio público."><a href="{{ route('admin.services.create') }}"><x-button size="sm">Crear servicio</x-button></a></x-empty-state></div>
            @else
                <x-table>
                    <thead><tr><th scope="col" class="px-5 py-3 text-left">Servicio</th><th scope="col" class="px-5 py-3 text-left">Slug</th><th scope="col" class="px-5 py-3 text-left">Orden</th><th scope="col" class="px-5 py-3 text-left">Estado</th><th scope="col" class="px-5 py-3 text-right">Acciones</th></tr></thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td class="px-5 py-4"><p class="font-medium text-slate-900">{{ $service->title }}</p><p class="mt-1 max-w-xl text-xs text-slate-500">{{ $service->summary }}</p></td>
                                <td class="px-5 py-4 font-mono text-xs text-slate-500">{{ $service->slug }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $service->position }}</td>
                                <td class="px-5 py-4"><x-badge :variant="$service->active ? 'success' : 'neutral'">{{ $service->active ? 'Activo' : 'Inactivo' }}</x-badge></td>
                                <td class="px-5 py-4"><div class="flex justify-end gap-2"><a href="{{ route('admin.services.edit', $service) }}"><x-button size="sm" variant="secondary">Editar</x-button></a><form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('¿Eliminar este servicio?')">@csrf @method('DELETE')<x-button type="submit" size="sm" variant="danger">Eliminar</x-button></form></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
                <div class="border-t border-slate-200 px-5 py-4">{{ $services->links() }}</div>
            @endif
        </x-card>
    </div>
@endsection

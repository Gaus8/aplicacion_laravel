@extends('layouts.admin')

@section('title', 'Banners')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <x-breadcrumb :items="[['label' => 'Contenido'], ['label' => 'Banners']]" />
                <h1 class="mt-3 font-display text-headline-xl-mobile font-bold text-slate-900 sm:text-headline-xl">Banners / Hero</h1>
                <p class="mt-2 text-sm text-slate-600">Gestiona las imágenes y mensajes que aparecen en el inicio público.</p>
            </div>
            <a href="{{ route('admin.banners.create') }}"><x-button><span aria-hidden="true">＋</span> Nuevo banner</x-button></a>
        </div>

        @if(session('success'))<x-alert variant="success">{{ session('success') }}</x-alert>@endif

        <x-card>
            <form action="{{ route('admin.banners.index') }}" method="GET" class="grid gap-3 sm:grid-cols-[1fr_12rem_auto] sm:items-end">
                <x-input name="search" label="Buscar por título" placeholder="Ej. Bienvenido" :value="$filters['search'] ?? ''" />
                <x-select name="status" label="Estado">
                    <option value="">Todos</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Activos</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactivos</option>
                </x-select>
                <x-button type="submit" variant="secondary">Filtrar</x-button>
            </form>
        </x-card>

        <x-card padding="none" class="overflow-hidden">
            @if($banners->isEmpty())
                <div class="p-8"><x-empty-state title="Todavía no hay banners" description="Crea un banner para comenzar a publicar contenido en la página de inicio."><a href="{{ route('admin.banners.create') }}"><x-button size="sm">Crear banner</x-button></a></x-empty-state></div>
            @else
                <div class="overflow-x-auto">
                    <x-table>
                        <thead><tr><th scope="col" class="px-5 py-3 text-left">Banner</th><th scope="col" class="px-5 py-3 text-left">Orden</th><th scope="col" class="px-5 py-3 text-left">Programación</th><th scope="col" class="px-5 py-3 text-left">Estado</th><th scope="col" class="px-5 py-3 text-right">Acciones</th></tr></thead>
                        <tbody>
                            @foreach($banners as $banner)
                                <tr>
                                    <td class="px-5 py-4"><div class="flex min-w-64 items-center gap-3"><img src="{{ Storage::disk('public')->url($banner->image_path) }}" alt="" class="h-14 w-24 rounded-md object-cover"><div class="min-w-0"><p class="truncate font-medium text-slate-900">{{ $banner->title }}</p><p class="truncate text-xs text-slate-500">{{ $banner->subtitle ?: 'Sin subtítulo' }}</p></div></div></td>
                                    <td class="px-5 py-4 text-sm text-slate-600">{{ $banner->position }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-600">{{ $banner->starts_at?->format('d/m/Y H:i') ?? 'Inmediata' }}<span class="block text-xs">{{ $banner->ends_at?->format('d/m/Y H:i') ?? 'Sin fecha de fin' }}</span></td>
                                    <td class="px-5 py-4"><x-badge :variant="$banner->active ? 'success' : 'neutral'">{{ $banner->active ? 'Activo' : 'Inactivo' }}</x-badge></td>
                                    <td class="px-5 py-4"><div class="flex justify-end gap-2"><a href="{{ route('admin.banners.edit', $banner) }}"><x-button size="sm" variant="secondary">Editar</x-button></a><form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('¿Eliminar este banner?')">@csrf @method('DELETE')<x-button type="submit" size="sm" variant="danger">Eliminar</x-button></form></div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-table>
                </div>
                <div class="border-t border-slate-200 px-5 py-4">{{ $banners->links() }}</div>
            @endif
        </x-card>
    </div>
@endsection

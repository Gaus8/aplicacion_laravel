@extends('layouts.admin')

@section('title', 'Galería multimedia')

@section('content')
    <div class="space-y-6">
        <x-breadcrumb :items="[['label' => 'Administración', 'url' => route('dashboard')], ['label' => 'Galería multimedia']]" />

        <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div><h1 class="font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Galería multimedia</h1><p class="mt-2 text-sm text-slate-600">Explora y administra las imágenes cargadas en la plataforma.</p></div>
            <a href="{{ route('admin.media.subirImagen') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-secondary px-4 py-2 text-sm font-semibold text-on-secondary shadow-sm transition hover:bg-secondary-container">＋ Subir imagen</a>
        </header>

        @if(session('success'))<x-alert variant="success" title="Operación completada">{{ session('success') }}</x-alert>@endif
        @if(session('error'))<x-alert variant="danger" title="No se pudo completar la operación">{{ session('error') }}</x-alert>@endif

        <x-card title="Archivos" :description="$media->total() . ' imágenes en la galería'" padding="none">
            <x-slot:actions>
                <form action="{{ route('admin.media.index') }}" method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                    <x-input label="Buscar por nombre" name="search" value="{{ $search }}" placeholder="Buscar imagen..." class="sm:w-64" />
                    <div class="flex items-center gap-2"><x-button type="submit" variant="secondary">Buscar</x-button>@if($search !== '')<a href="{{ route('admin.media.index') }}" class="px-2 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">Limpiar</a>@endif</div>
                </form>
            </x-slot:actions>

            @if($media->count())
                <div class="grid gap-4 border-t border-slate-100 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($media as $asset)
                        @php($formId = 'delete-media-' . $asset->id)
                        @php($dialogId = 'confirm-delete-media-' . $asset->id)
                        <article class="overflow-hidden rounded-lg border border-slate-200 bg-white transition hover:shadow-card">
                            <a href="{{ route('admin.media.file', $asset) }}" target="_blank" rel="noopener noreferrer" class="group relative block aspect-[4/3] overflow-hidden bg-surface-container-low" aria-label="Abrir imagen {{ $asset->name }}">
                                <img src="{{ route('admin.media.file', $asset) }}" alt="{{ $asset->name }}" loading="lazy" class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.02]">
                                <span class="absolute bottom-2 right-2"><x-badge variant="success">Imagen</x-badge></span>
                            </a>
                            <div class="space-y-3 p-4">
                                <div><h2 class="truncate text-sm font-semibold text-slate-900" title="{{ $asset->name }}">{{ $asset->name }}</h2><p class="mt-1 text-xs text-slate-500">{{ $asset->mime_type ?: 'Tipo desconocido' }} · {{ number_format(($asset->size ?? 0) / 1024, 1) }} KB</p></div>
                                <div class="flex items-center justify-between border-t border-slate-100 pt-3"><span class="text-xs tabular-nums text-slate-500">{{ $asset->created_at?->format('d/m/Y') }}</span><div class="flex items-center gap-3"><a href="{{ route('admin.media.file', $asset) }}" target="_blank" rel="noopener noreferrer" class="text-xs font-medium text-secondary hover:underline">Ver imagen</a><button type="button" data-modal-open="{{ $dialogId }}" class="text-xs font-medium text-rose-700 hover:underline">Eliminar</button></div></div>
                            </div>
                        </article>
                        <form id="{{ $formId }}" action="{{ route('admin.media.destroy', $asset) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                        <x-confirm-dialog :id="$dialogId" title="¿Eliminar esta imagen?" description="El archivo se quitará de la galería y no se podrá recuperar." confirm-text="Eliminar imagen" confirm-type="submit" :confirm-form="$formId">{{ $asset->name }}</x-confirm-dialog>
                    @endforeach
                </div>
                <div class="border-t border-slate-100 p-4"><x-pagination :current-page="$media->currentPage()" :total-pages="$media->lastPage()" /></div>
            @else
                <div class="border-t border-slate-100 p-4"><x-empty-state title="No se encontraron imágenes" description="Carga una imagen o cambia el texto de búsqueda para ver resultados."><x-slot:action><a href="{{ route('admin.media.subirImagen') }}" class="inline-flex items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-medium text-on-secondary hover:bg-secondary-container">Subir imagen</a></x-slot:action></x-empty-state></div>
            @endif
        </x-card>
    </div>
@endsection

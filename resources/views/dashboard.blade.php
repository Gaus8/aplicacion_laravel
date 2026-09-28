@extends('layouts.admin')

@section('title', 'Panel de control')

@section('content')
    <div class="space-y-8">
        <header>
            <x-breadcrumb :items="[['label' => 'Administración'], ['label' => 'Panel de control']]" class="mb-3" />
            <h1 class="font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Panel de control</h1>
            <p class="mt-2 text-sm text-slate-600">Resumen de tu cuenta y del contenido almacenado.</p>
        </header>

        <section aria-label="Resumen de cuenta" class="grid gap-4 md:grid-cols-3">
            <x-card padding="md">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Usuario conectado</p>
                <p class="mt-2 truncate font-display text-lg font-semibold text-slate-900" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                <p class="mt-1 text-sm text-slate-500">Cuenta autenticada</p>
            </x-card>
            <x-card padding="md">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Correo registrado</p>
                <p class="mt-2 truncate font-display text-lg font-semibold text-slate-900" title="{{ auth()->user()->email }}">{{ auth()->user()->email }}</p>
                <p class="mt-1 text-sm text-slate-500">Dirección de la cuenta</p>
            </x-card>
            <x-card padding="md">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Archivos en galería</p>
                <p class="mt-2 font-display text-2xl font-semibold tabular-nums text-slate-900">{{ count($media ?? []) }}</p>
                <p class="mt-1 text-sm text-slate-500">Elementos multimedia</p>
            </x-card>
        </section>

        <x-card title="Galería de archivos" description="Archivos multimedia cargados en la plataforma." padding="none">
            <x-slot:actions>
                <a href="{{ route('admin.media.subirImagen') }}" class="inline-flex items-center gap-2 rounded-md bg-secondary px-3.5 py-2 text-sm font-medium text-on-secondary shadow-sm transition hover:bg-secondary-container"><span aria-hidden="true">＋</span>Subir imagen</a>
            </x-slot:actions>
            @if(isset($media) && count($media) > 0)
                <div class="grid gap-4 border-t border-slate-100 p-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($media as $item)
                        <article class="overflow-hidden rounded-lg border border-slate-200 bg-white transition hover:shadow-card">
                            <a href="{{ Storage::url($item->path) }}" target="_blank" rel="noopener noreferrer" class="block aspect-[16/10] bg-surface-container"><img src="{{ Storage::url($item->path) }}" alt="{{ $item->name }}" class="h-full w-full object-cover"></a>
                            <div class="px-4 py-3"><h3 class="truncate text-sm font-semibold text-slate-800" title="{{ $item->name }}">{{ $item->name }}</h3></div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="border-t border-slate-100 p-5"><x-empty-state title="Todavía no hay imágenes" description="Sube una imagen para verla en esta galería."><x-slot:action><a href="{{ route('admin.media.subirImagen') }}" class="inline-flex items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-medium text-on-secondary hover:bg-secondary-container">Subir imagen</a></x-slot:action></x-empty-state></div>
            @endif
        </x-card>

        <x-card title="Correos electrónicos" description="Registro de los mensajes enviados desde la plataforma." padding="none">
            <x-slot:actions>
                <a href="{{ route('admin.media.emails') }}" class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50">Enviar correo</a>
            </x-slot:actions>
            @if(isset($emails) && count($emails) > 0)
                <x-table caption="Correos enviados" class="rounded-none border-0 border-t border-slate-100 shadow-none">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500"><tr><th scope="col" class="px-5 py-3 font-semibold">Destinatario</th><th scope="col" class="px-5 py-3 font-semibold">Asunto</th><th scope="col" class="px-5 py-3 font-semibold">Fecha de envío</th><th scope="col" class="px-5 py-3 font-semibold">Estado</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">@foreach($emails as $email)<tr><td class="px-5 py-4 text-sm text-slate-700">{{ $email->recipient }}</td><td class="px-5 py-4 text-sm text-slate-700">{{ $email->subject }}</td><td class="px-5 py-4 text-sm tabular-nums text-slate-500">{{ $email->created_at->format('d/m/Y H:i') }}</td><td class="px-5 py-4"><x-badge variant="success">Enviado</x-badge></td></tr>@endforeach</tbody>
                </x-table>
            @else
                <div class="border-t border-slate-100 p-5"><x-empty-state title="No hay correos registrados" description="Los mensajes enviados aparecerán aquí." /></div>
            @endif
        </x-card>
    </div>
@endsection

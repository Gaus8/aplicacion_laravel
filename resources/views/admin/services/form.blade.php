@extends('layouts.admin')

@section('title', $isEditing ? 'Editar servicio' : 'Nuevo servicio')

@section('content')
    <div class="mx-auto w-full max-w-5xl space-y-6">
        <div>
            <x-breadcrumb :items="[['label' => 'Servicios', 'url' => route('admin.services.index')], ['label' => $isEditing ? 'Editar' : 'Nuevo']]" />
            <h1 class="mt-3 font-display text-headline-xl-mobile font-bold text-slate-900 sm:text-headline-xl">{{ $isEditing ? 'Editar servicio' : 'Crear servicio' }}</h1>
            <p class="mt-2 text-sm text-slate-600">El slug se genera automáticamente a partir del título.</p>
        </div>

        @if($errors->any())<x-alert variant="danger"><p class="font-semibold">Revisa los campos indicados:</p><ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert>@endif

        <x-card>
            <form action="{{ $isEditing ? route('admin.services.update', $service) : route('admin.services.store') }}" method="POST" class="space-y-6">
                @csrf
                @if($isEditing) @method('PUT') @endif
                <div class="grid gap-5 md:grid-cols-2">
                    <x-input name="title" label="Nombre del servicio" maxlength="120" required :value="old('title', $service->title)" />
                    <x-input name="position" label="Orden de aparición" type="number" min="0" max="10000" required :value="old('position', $service->position ?? 0)" />
                    <div class="md:col-span-2"><x-input name="summary" label="Resumen" maxlength="240" required :value="old('summary', $service->summary)" hint="Una frase breve para presentar el servicio." /></div>
                    <div class="md:col-span-2"><x-textarea name="description" label="Descripción" rows="6" maxlength="10000" required hint="Descripción que verá el público.">{{ old('description', $service->description) }}</x-textarea></div>
                    <x-input name="cta_label" label="Texto del enlace (opcional)" maxlength="60" :value="old('cta_label', $service->cta_label)" />
                    <x-input name="cta_url" label="URL del enlace (opcional)" type="url" maxlength="2048" placeholder="https://ejemplo.com" :value="old('cta_url', $service->cta_url)" />
                </div>
                <x-checkbox name="active" value="1" :checked="old('active', $service->active)" label="Publicar servicio" description="Los servicios inactivos no aparecen en el sitio público." />
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end"><a href="{{ route('admin.services.index') }}"><x-button variant="secondary">Cancelar</x-button></a><x-button type="submit">{{ $isEditing ? 'Guardar cambios' : 'Crear servicio' }}</x-button></div>
            </form>
        </x-card>
    </div>
@endsection

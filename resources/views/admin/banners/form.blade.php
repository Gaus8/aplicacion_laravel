@extends('layouts.admin')

@section('title', $isEditing ? 'Editar banner' : 'Nuevo banner')

@section('content')
    <div class="mx-auto w-full max-w-5xl space-y-6">
        <div>
            <x-breadcrumb :items="[['label' => 'Banners', 'url' => route('admin.banners.index')], ['label' => $isEditing ? 'Editar' : 'Nuevo']]" />
            <h1 class="mt-3 font-display text-headline-xl-mobile font-bold text-slate-900 sm:text-headline-xl">{{ $isEditing ? 'Editar banner' : 'Crear banner' }}</h1>
            <p class="mt-2 text-sm text-slate-600">La imagen se valida por tipo real y se guarda en el almacenamiento público de Laravel.</p>
        </div>

        @if($errors->any())<x-alert variant="danger"><p class="font-semibold">Revisa los campos indicados:</p><ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert>@endif

        <x-card>
            <form action="{{ $isEditing ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if($isEditing) @method('PUT') @endif
                <div class="grid gap-5 md:grid-cols-2">
                    <x-input name="title" label="Título del banner" maxlength="80" required :value="old('title', $banner->title)" hint="De 5 a 80 caracteres." />
                    <x-input name="image_alt" label="Texto alternativo de imagen" maxlength="180" required :value="old('image_alt', $banner->image_alt)" hint="Describe brevemente la imagen para lectores de pantalla." />
                    <div class="md:col-span-2"><x-textarea name="subtitle" label="Subtítulo" rows="3" maxlength="180" :hint="'Hasta 180 caracteres.'">{{ old('subtitle', $banner->subtitle) }}</x-textarea></div>
                    <x-input name="cta_label" label="Texto del botón (opcional)" maxlength="40" :value="old('cta_label', $banner->cta_label)" />
                    <x-input name="cta_url" label="URL del botón (opcional)" type="url" maxlength="2048" placeholder="https://ejemplo.com" :value="old('cta_url', $banner->cta_url)" />
                    <x-input name="position" label="Orden" type="number" min="0" max="10000" required :value="old('position', $banner->position ?? 0)" />
                    <div class="space-y-1.5"><label for="image" class="block text-sm font-medium text-slate-700">Imagen {{ $isEditing ? '(opcional para conservar la actual)' : '' }}</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" @required(!$isEditing) class="block w-full rounded-md border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-secondary-fixed file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-on-secondary-fixed"><p class="text-xs text-slate-500">JPG, PNG o WebP; máximo 5 MB.</p>@error('image')<p class="text-xs text-rose-700">{{ $message }}</p>@enderror</div>
                    <x-input name="starts_at" label="Mostrar desde (opcional)" type="datetime-local" :value="old('starts_at', $banner->starts_at?->format('Y-m-d\TH:i'))" />
                    <x-input name="ends_at" label="Mostrar hasta (opcional)" type="datetime-local" :value="old('ends_at', $banner->ends_at?->format('Y-m-d\TH:i'))" />
                </div>
                @if($isEditing)<div class="rounded-lg bg-slate-50 p-4"><img src="{{ Storage::disk('public')->url($banner->image_path) }}" alt="{{ $banner->image_alt }}" class="max-h-56 w-full rounded-md object-cover"></div>@endif
                <x-checkbox name="active" value="1" :checked="old('active', $banner->active)" label="Publicar este banner" description="Solo se muestran banners activos dentro de su periodo programado." />
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end"><a href="{{ route('admin.banners.index') }}"><x-button variant="secondary">Cancelar</x-button></a><x-button type="submit">{{ $isEditing ? 'Guardar cambios' : 'Crear banner' }}</x-button></div>
            </form>
        </x-card>
    </div>
@endsection

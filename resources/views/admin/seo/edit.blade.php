@extends('layouts.admin')
@section('title', 'SEO')
@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div><h1 class="font-display text-2xl font-semibold">Configuración SEO</h1><p class="mt-1 text-sm text-slate-600">Metadatos predeterminados y directivas públicas del sitio.</p></div>
    @if(session('success'))<x-alert type="success">{{ session('success') }}</x-alert>@endif
    <x-card>
        <form method="POST" action="{{ route('admin.seo.update') }}" enctype="multipart/form-data" class="space-y-5">@csrf @method('PUT')
            <x-input name="site_name" label="Nombre del sitio" :value="old('site_name', $setting->site_name)" required />
            <x-input name="title_template" label="Plantilla del título (usa %s para el título de página)" :value="old('title_template', $setting->title_template)" required />
            <x-input name="default_title" label="Título predeterminado" :value="old('default_title', $setting->default_title)" required />
            <x-textarea name="default_description" label="Descripción predeterminada" rows="3" required>{{ old('default_description', $setting->default_description) }}</x-textarea>
            <x-input name="canonical_base_url" type="url" label="URL canónica base (HTTPS)" :value="old('canonical_base_url', $setting->canonical_base_url)" placeholder="https://example.com" />
            <x-select name="robots_directive" label="Indexación">
                @foreach(['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'] as $directive)<option value="{{ $directive }}" @selected(old('robots_directive', $setting->robots_directive) === $directive)>{{ $directive }}</option>@endforeach
            </x-select>
            <x-input name="og_image" type="file" label="Imagen Open Graph (JPG, PNG o WebP; máximo 5 MB)" accept="image/jpeg,image/png,image/webp" />
            @if($setting->og_image_path)<img src="{{ Storage::disk('public')->url($setting->og_image_path) }}" alt="Imagen Open Graph actual" class="h-28 w-48 rounded-lg object-cover">@endif
            <div class="flex justify-end"><x-button type="submit">Guardar configuración</x-button></div>
        </form>
    </x-card>
</div>
@endsection

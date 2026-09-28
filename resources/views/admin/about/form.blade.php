@extends('layouts.admin')
@section('title', 'Nosotros / Institucional')
@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div><x-breadcrumb :items="[['label' => 'Contenido'], ['label' => 'Nosotros']]"/><h1 class="mt-3 font-display text-headline-xl-mobile font-bold text-slate-900 sm:text-headline-xl">Contenido institucional</h1><p class="mt-2 text-sm text-slate-600">Una única página institucional que se publica en cuanto queda activa.</p></div>
    @if(session('success'))<x-alert variant="success">{{ session('success') }}</x-alert>@endif
    @if($errors->any())<x-alert variant="danger"><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert>@endif
    <x-card><form action="{{ route('admin.about.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">@csrf @method('PUT')
        <div class="grid gap-5 md:grid-cols-2"><x-input name="title" label="Título" maxlength="140" required :value="old('title', $page->title)"/><x-input name="subtitle" label="Subtítulo" maxlength="240" :value="old('subtitle', $page->subtitle)"/><div class="md:col-span-2"><x-textarea name="body" label="Presentación institucional" rows="7" required maxlength="30000">{{ old('body', $page->body) }}</x-textarea></div><x-textarea name="mission" label="Misión" rows="4" maxlength="5000">{{ old('mission', $page->mission) }}</x-textarea><x-textarea name="vision" label="Visión" rows="4" maxlength="5000">{{ old('vision', $page->vision) }}</x-textarea>
        <div><label for="image" class="mb-1.5 block text-sm font-medium text-slate-700">Imagen institucional</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm"><p class="mt-1 text-xs text-slate-500">JPG, PNG o WebP; máximo 5 MB.</p>@error('image')<p class="text-xs text-rose-700">{{ $message }}</p>@enderror</div><x-input name="image_alt" label="Texto alternativo" maxlength="180" :value="old('image_alt', $page->image_alt)"/></div>
        @if($page->image_path)<img src="{{ Storage::disk('public')->url($page->image_path) }}" alt="{{ $page->image_alt }}" class="max-h-64 rounded-lg object-cover">@endif
        <x-checkbox name="active" value="1" :checked="old('active', $page->active)" label="Publicar en /nosotros" description="Los cambios se guardan aunque esta página esté desactivada."/>
        <div class="flex justify-end border-t border-slate-200 pt-5"><x-button type="submit">Guardar página</x-button></div>
    </form></x-card>
</div>
@endsection

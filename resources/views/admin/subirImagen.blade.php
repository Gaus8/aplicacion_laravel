@extends('layouts.admin')

@section('title', 'Subir imagen')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <x-breadcrumb :items="[['label' => 'Administración', 'url' => route('dashboard')], ['label' => 'Galería', 'url' => route('dashboard')], ['label' => 'Subir imagen']]" />
        <header><h1 class="font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Subir imagen</h1><p class="mt-2 text-sm text-slate-600">Añade una imagen a la galería multimedia.</p></header>

        <x-card title="Detalles del archivo" description="Completa el nombre y selecciona la imagen que quieres cargar.">
            @if($errors->any())<x-alert class="mb-5" variant="danger" title="No se pudo guardar el archivo">Revisa la información e inténtalo nuevamente.</x-alert>@endif
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <x-input label="Nombre del archivo" name="name" value="{{ old('name') }}" placeholder="Ej. Imagen de portada" required />
                <div>
                    <label for="file-input" class="mb-1.5 block text-sm font-medium text-slate-700">Imagen</label>
                    <label for="file-input" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-surface-container-low px-5 py-10 text-center transition hover:border-secondary hover:bg-indigo-50/40">
                        <span aria-hidden="true" class="mb-3 grid h-12 w-12 place-items-center rounded-lg bg-white text-secondary shadow-sm"><svg viewBox="0 0 24 24" fill="none" class="h-6 w-6"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M4 15v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <span class="text-sm font-semibold text-slate-800">Haz clic para seleccionar una imagen</span>
                        <span class="mt-1 text-xs text-slate-500">PNG, JPG, JPEG o WEBP</span>
                        <input type="file" id="file-input" name="file" accept="image/*" required class="sr-only">
                    </label>
                    @error('file')<p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p>@enderror
                    <div id="preview-wrapper" class="mt-4 hidden overflow-hidden rounded-lg border border-slate-200 bg-white p-2"><img id="preview" src="#" alt="Vista previa de la imagen seleccionada" class="max-h-80 w-full rounded object-contain"></div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 pt-5"><a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancelar</a><x-button type="submit">Subir archivo</x-button></div>
            </form>
        </x-card>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('file-input')?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        const wrapper = document.getElementById('preview-wrapper');
        const preview = document.getElementById('preview');
        if (!wrapper || !preview) return;
        if (!file) {
            wrapper.classList.add('hidden');
            preview.removeAttribute('src');
            return;
        }
        const reader = new FileReader();
        reader.addEventListener('load', () => {
            preview.src = reader.result;
            wrapper.classList.remove('hidden');
        });
        reader.readAsDataURL(file);
    });
</script>
@endpush

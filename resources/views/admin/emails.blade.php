@extends('layouts.admin')

@section('title', 'Enviar correo')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <x-breadcrumb :items="[['label' => 'Administración', 'url' => route('dashboard')], ['label' => 'Correos electrónicos']]" />
        <header><h1 class="font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Enviar correo electrónico</h1><p class="mt-2 text-sm text-slate-600">Escribe el mensaje que quieres enviar desde la plataforma.</p></header>

        <x-card title="Redactar mensaje" description="Completa el destinatario, el asunto y el contenido del mensaje.">
            @if($errors->any())<x-alert class="mb-5" variant="danger" title="No se pudo enviar el correo">Revisa los datos e inténtalo nuevamente.</x-alert>@endif
            <form action="{{ route('admin.media.sendEmail') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <x-input label="Correo destinatario" type="email" name="recipient" value="{{ old('recipient') }}" required placeholder="ejemplo@correo.com" />
                <x-input label="Asunto" type="text" name="subject" value="{{ old('subject') }}" required placeholder="Asunto del correo" />
                <x-textarea label="Mensaje" name="message" rows="6" required placeholder="Escribe el contenido del mensaje...">{{ old('message') }}</x-textarea>
                <div>
                    <label for="attachments" class="mb-1.5 block text-sm font-medium text-slate-700">Archivos adjuntos <span class="font-normal text-slate-500">(opcional)</span></label>
                    <label for="attachments" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-surface-container-low px-5 py-8 text-center transition hover:border-secondary hover:bg-indigo-50/40">
                        <span aria-hidden="true" class="mb-2 text-2xl text-secondary">⌁</span><span class="text-sm font-medium text-slate-700">Selecciona uno o varios archivos</span><span class="mt-1 text-xs text-slate-500">Imágenes, PDF, DOCX o ZIP · Máx. 10 MB por archivo</span>
                        <input type="file" name="attachments[]" id="attachments" multiple class="sr-only">
                    </label>
                    <ul id="file-list" class="mt-2 space-y-1 text-xs text-slate-600" aria-live="polite"></ul>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 pt-5"><a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancelar</a><x-button type="submit">Enviar correo</x-button></div>
            </form>
        </x-card>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('attachments')?.addEventListener('change', (event) => {
        const fileList = document.getElementById('file-list');
        if (!fileList) return;
        fileList.replaceChildren();
        Array.from(event.target.files ?? []).forEach((file) => {
            const item = document.createElement('li');
            item.textContent = `${file.name} · ${(file.size / 1024 / 1024).toFixed(2)} MB`;
            fileList.append(item);
        });
    });
</script>
@endpush

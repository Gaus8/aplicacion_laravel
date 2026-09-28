@props(['id', 'title', 'description' => null, 'size' => 'md'])
@php($sizes = ['sm' => 'max-w-sm', 'md' => 'max-w-lg', 'lg' => 'max-w-2xl'])
<dialog id="{{ $id }}" {{ $attributes->class(['w-[calc(100%-2rem)] rounded-lg border border-slate-200 bg-white p-0 text-slate-900 shadow-dialog backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm', $sizes[$size] ?? $sizes['md']]) }} aria-labelledby="{{ $id }}-title">
    <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5"><div><h2 id="{{ $id }}-title" class="font-display text-lg font-semibold">{{ $title }}</h2>@if($description)<p class="mt-1 text-sm text-slate-500">{{ $description }}</p>@endif</div><button type="button" data-modal-close="{{ $id }}" class="rounded p-1 text-slate-500 hover:bg-slate-100" aria-label="Cerrar diálogo">✕</button></div>
    <div class="p-6">{{ $slot }}</div>
</dialog>

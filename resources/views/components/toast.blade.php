@props(['id' => 'design-toast', 'variant' => 'success', 'title' => 'Cambios guardados'])
@php($variants = ['success' => 'border-emerald-500', 'info' => 'border-blue-500', 'warning' => 'border-amber-500', 'danger' => 'border-rose-500'])
<div id="{{ $id }}" role="status" aria-live="polite" data-toast {{ $attributes->class(['hidden fixed bottom-4 right-4 z-[60] max-w-sm items-start gap-3 rounded-lg border-l-4 bg-slate-900 px-4 py-3 text-sm text-white shadow-toast', $variants[$variant] ?? $variants['success']]) }}>
    <div class="flex-1"><p class="font-semibold">{{ $title }}</p><div class="mt-0.5 text-slate-300">{{ $slot }}</div></div><button type="button" data-toast-close class="text-slate-300 hover:text-white" aria-label="Cerrar notificación">✕</button>
</div>

@props(['variant' => 'info', 'title' => null, 'dismissible' => false])
@php($variants = ['info' => 'border-blue-200 bg-blue-50 text-blue-900', 'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900', 'warning' => 'border-amber-200 bg-amber-50 text-amber-950', 'danger' => 'border-rose-200 bg-rose-50 text-rose-900'])
<div role="alert" {{ $attributes->class(['flex items-start gap-3 rounded-md border p-4 text-sm', $variants[$variant] ?? $variants['info']]) }}>
    <div class="min-w-0 flex-1">@if($title)<p class="font-semibold">{{ $title }}</p>@endif<div class="{{ $title ? 'mt-1' : '' }}">{{ $slot }}</div></div>
    @if($dismissible)<button type="button" data-dismiss class="rounded px-1 font-bold opacity-70 hover:opacity-100" aria-label="Descartar alerta">✕</button>@endif
</div>

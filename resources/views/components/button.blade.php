@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
])
@php
    $variants = [
        'primary' => 'bg-secondary text-on-secondary hover:bg-secondary-container focus-visible:outline-secondary',
        'secondary' => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus-visible:outline-slate-500',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus-visible:outline-rose-500',
        'ghost' => 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-slate-500',
    ];
    $sizes = ['sm' => 'px-3 py-1.5 text-xs', 'md' => 'px-4 py-2 text-sm', 'lg' => 'px-5 py-2.5 text-base'];
@endphp
<button type="{{ $type }}" @disabled($disabled) {{ $attributes->class(['inline-flex items-center justify-center gap-2 rounded-md font-medium shadow-sm transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 disabled:pointer-events-none disabled:opacity-50', $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md']]) }}>
    {{ $slot }}
</button>

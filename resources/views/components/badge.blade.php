@props(['variant' => 'neutral', 'size' => 'md'])
@php
    $variants = ['neutral' => 'border-slate-200 bg-slate-100 text-slate-700', 'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700', 'warning' => 'border-amber-200 bg-amber-50 text-amber-700', 'danger' => 'border-rose-200 bg-rose-50 text-rose-700', 'info' => 'border-blue-200 bg-blue-50 text-blue-700', 'primary' => 'border-indigo-200 bg-indigo-50 text-indigo-700'];
    $sizes = ['sm' => 'px-2 py-0.5 text-[10px]', 'md' => 'px-2.5 py-0.5 text-[11px]'];
@endphp
<span {{ $attributes->class(['inline-flex items-center rounded-full border font-semibold uppercase tracking-wide', $variants[$variant] ?? $variants['neutral'], $sizes[$size] ?? $sizes['md']]) }}>{{ $slot }}</span>

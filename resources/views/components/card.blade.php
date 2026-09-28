@props(['title' => null, 'description' => null, 'padding' => 'md'])
@php($paddings = ['none' => '', 'sm' => 'p-4', 'md' => 'p-6', 'lg' => 'p-8'])
<section {{ $attributes->class(['rounded-lg border border-slate-200/80 bg-white shadow-card', $paddings[$padding] ?? $paddings['md']]) }}>
    @if($title || $description)<header @class(['mb-5', 'px-5 pt-5' => $padding === 'none'])><h2 class="font-display text-lg font-semibold text-slate-900">{{ $title }}</h2>@if($description)<p class="mt-1 text-sm text-slate-500">{{ $description }}</p>@endif</header>@endif
    {{ $slot }}
</section>

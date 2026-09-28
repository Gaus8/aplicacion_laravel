@props(['title', 'description' => null, 'icon' => '◇'])
<div {{ $attributes->class(['flex flex-col items-center justify-center rounded-lg border border-dashed border-slate-300 bg-white px-6 py-12 text-center']) }}>
    <span aria-hidden="true" class="mb-3 text-3xl text-slate-400">{{ $icon }}</span><h3 class="font-display text-base font-semibold text-slate-900">{{ $title }}</h3>@if($description)<p class="mt-1 max-w-md text-sm text-slate-500">{{ $description }}</p>@endif
    @if(isset($action))<div class="mt-5">{{ $action }}</div>@endif
    {{ $slot }}
</div>

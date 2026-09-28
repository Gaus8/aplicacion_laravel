@props(['label' => null, 'description' => null])
<label {{ $attributes->only('class')->class(['inline-flex cursor-pointer items-center gap-3']) }}>
    <input type="checkbox" role="switch" {{ $attributes->except('class')->class(['peer sr-only']) }}>
    <span aria-hidden="true" class="relative h-6 w-11 rounded-full bg-slate-300 transition-colors peer-checked:bg-secondary peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-secondary peer-disabled:opacity-50 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:after:translate-x-5"></span>
    <span class="text-sm text-slate-700">@if($label)<span class="font-medium">{{ $label }}</span>@endif @if($description)<span class="block text-xs text-slate-500">{{ $description }}</span>@endif{{ $slot }}</span>
</label>

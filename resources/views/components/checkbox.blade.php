@props(['label' => null, 'description' => null, 'error' => null])
<label {{ $attributes->only('class')->class(['flex items-start gap-2.5 text-sm text-slate-700']) }}>
    <input type="checkbox" {{ $attributes->except('class')->class(['mt-0.5 h-4 w-4 rounded border-slate-300 text-secondary focus:ring-secondary disabled:opacity-50']) }} @if($error) aria-invalid="true" @endif>
    <span>@if($label)<span class="font-medium">{{ $label }}</span>@endif @if($description)<span class="block text-xs text-slate-500">{{ $description }}</span>@endif{{ $slot }}</span>
</label>

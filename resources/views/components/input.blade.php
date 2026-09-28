@props(['label' => null, 'error' => null, 'hint' => null, 'id' => null])
@php($fieldId = $id ?? $attributes->get('name') ?? 'input-' . uniqid())
<div {{ $attributes->only('class')->class(['space-y-1.5']) }}>
    @if($label)<label for="{{ $fieldId }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>@endif
    <div class="relative">
        <input id="{{ $fieldId }}" {{ $attributes->class(['block w-full rounded-md border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-secondary focus:ring-1 focus:ring-secondary disabled:bg-slate-50 disabled:text-slate-400', 'border-rose-400' => $error]) }} @if($error) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @endif>
        {{ $slot }}
    </div>
    @if($hint && !$error)<p class="text-xs text-slate-500">{{ $hint }}</p>@endif
    @if($error)<p id="{{ $fieldId }}-error" class="text-xs text-rose-700">{{ $error }}</p>@endif
</div>

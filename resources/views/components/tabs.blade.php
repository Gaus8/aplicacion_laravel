@props(['id', 'tabs'])
<div id="{{ $id }}" data-tabs>
    <div role="tablist" aria-label="{{ $attributes->get('aria-label', 'Pestañas') }}" class="flex gap-1 overflow-x-auto border-b border-slate-200">
        @foreach($tabs as $key => $label)<button id="{{ $id }}-tab-{{ $key }}" type="button" role="tab" aria-controls="{{ $id }}-panel-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}" data-tab-trigger="{{ $key }}" class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium {{ $loop->first ? 'border-secondary text-secondary' : 'border-transparent text-slate-500 hover:text-slate-800' }}">{{ $label }}</button>@endforeach
    </div>
    <div class="pt-4">{{ $slot }}</div>
</div>

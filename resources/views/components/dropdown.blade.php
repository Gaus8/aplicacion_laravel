@props(['label' => 'Acciones'])
<details {{ $attributes->class(['group relative inline-block']) }}>
    <summary class="inline-flex cursor-pointer list-none items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">{{ $label }}<span aria-hidden="true" class="text-xs">▾</span></summary>
    <div data-dropdown-menu class="absolute right-0 z-20 mt-2 min-w-44 rounded-md border border-slate-200 bg-white p-1 shadow-popover">{{ $slot }}</div>
</details>

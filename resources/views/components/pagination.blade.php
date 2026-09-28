@props(['currentPage' => 1, 'totalPages' => 1])
@php($totalPages = max(1, (int) $totalPages))
<nav aria-label="Paginación" {{ $attributes->class(['flex items-center justify-between gap-4 text-sm']) }}>
    <span class="text-slate-500">Página {{ $currentPage }} de {{ $totalPages }}</span>
    <div class="flex items-center gap-1">
        <a href="?page={{ max(1, $currentPage - 1) }}" @if($currentPage <= 1) aria-disabled="true" tabindex="-1" @endif class="rounded-md border border-slate-300 px-3 py-1.5 text-slate-700 hover:bg-slate-50 aria-disabled:pointer-events-none aria-disabled:opacity-50">Anterior</a>
        @for($page = 1; $page <= $totalPages; $page++)<a href="?page={{ $page }}" aria-label="Página {{ $page }}" @if($page == $currentPage) aria-current="page" @endif class="rounded-md border px-3 py-1.5 {{ $page == $currentPage ? 'border-secondary bg-indigo-50 text-secondary' : 'border-slate-300 text-slate-700 hover:bg-slate-50' }}">{{ $page }}</a>@endfor
        <a href="?page={{ min($totalPages, $currentPage + 1) }}" @if($currentPage >= $totalPages) aria-disabled="true" tabindex="-1" @endif class="rounded-md border border-slate-300 px-3 py-1.5 text-slate-700 hover:bg-slate-50 aria-disabled:pointer-events-none aria-disabled:opacity-50">Siguiente</a>
    </div>
</nav>

@props(['caption' => null])
<div {{ $attributes->class(['overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-card']) }}>
    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
        @if($caption)<caption class="sr-only">{{ $caption }}</caption>@endif
        {{ $slot }}
    </table>
</div>

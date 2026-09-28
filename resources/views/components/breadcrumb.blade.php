@props(['items' => []])
<nav aria-label="Migas de pan" {{ $attributes }}><ol class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
    @foreach($items as $item)<li class="inline-flex items-center gap-2">@if(!$loop->first)<span aria-hidden="true" class="text-slate-300">/</span>@endif @if(!$loop->last && !empty($item['url']))<a href="{{ $item['url'] }}" class="hover:text-secondary">{{ $item['label'] }}</a>@else<span @if($loop->last) aria-current="page" class="font-medium text-slate-900" @endif>{{ $item['label'] }}</span>@endif</li>@endforeach
</ol></nav>

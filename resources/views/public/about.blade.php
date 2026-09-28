@extends('layouts.public')
@section('title', $page->title)
@section('content')
<article class="mx-auto w-full max-w-5xl flex-1 px-4 py-12 sm:px-6 lg:px-8">
    <x-breadcrumb :items="[['label' => 'Inicio', 'url' => route('home')], ['label' => 'Nosotros']]"/>
    <header class="mt-6 max-w-3xl"><h1 class="font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">{{ $page->title }}</h1>@if($page->subtitle)<p class="mt-4 text-body-lg text-slate-600">{{ $page->subtitle }}</p>@endif</header>
    @if($page->image_path)<div class="home-reveal relative mt-8 overflow-hidden rounded-xl bg-slate-900"><div class="absolute -inset-12 bg-secondary/20 blur-3xl" aria-hidden="true"></div><img src="{{ Storage::disk('public')->url($page->image_path) }}" alt="{{ $page->image_alt }}" class="home-story-image relative max-h-[32rem] w-full rounded-xl object-cover"></div>@endif
    <div class="mt-8 whitespace-pre-line leading-7 text-slate-700">{{ $page->body }}</div>
    @if($page->mission || $page->vision)<div class="mt-10 grid gap-5 md:grid-cols-2">@if($page->mission)<x-card title="Misión"><p class="whitespace-pre-line text-sm leading-6 text-slate-600">{{ $page->mission }}</p></x-card>@endif @if($page->vision)<x-card title="Visión"><p class="whitespace-pre-line text-sm leading-6 text-slate-600">{{ $page->vision }}</p></x-card>@endif</div>@endif
</article>
@endsection

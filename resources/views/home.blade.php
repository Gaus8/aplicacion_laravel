@extends('layouts.public')

@section('title', 'Inicio · CMS Core')

@section('content')
    @include('public.partials.hero-banners')
    <section class="mx-auto flex w-full max-w-7xl flex-1 items-center px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid w-full items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
            <div>
                <x-badge variant="primary">Plataforma de contenidos</x-badge>
                <h1 class="mt-6 max-w-2xl font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Contenido claro. Gestión sencilla.</h1>
                <p class="mt-5 max-w-xl text-body-lg text-slate-600">Un espacio central para administrar y publicar el contenido de tu organización.</p>
                @guest
                    <div class="mt-8"><a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-secondary px-5 py-2.5 text-base font-semibold text-on-secondary shadow-sm transition hover:bg-secondary-container focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary">Acceder al panel <span aria-hidden="true">→</span></a></div>
                @endguest
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-card sm:p-8">
                @auth
                    <div class="flex items-center gap-4">
                        <span class="grid h-12 w-12 place-items-center rounded-full bg-secondary-fixed text-lg font-semibold text-on-secondary-fixed">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                        <div class="min-w-0"><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sesión activa</p><p class="truncate font-display text-lg font-semibold text-slate-900">{{ auth()->user()->name }}</p><p class="truncate text-sm text-slate-500">{{ auth()->user()->email }}</p></div>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-3"><a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-secondary px-4 py-2 text-sm font-medium text-on-secondary shadow-sm transition hover:bg-secondary-container">Ir al panel</a><form action="{{ route('logout') }}" method="POST">@csrf<x-button type="submit" variant="secondary">Cerrar sesión</x-button></form></div>
                @else
                    <div class="mb-5 grid h-12 w-12 place-items-center rounded-lg bg-success-container text-success" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" class="h-6 w-6"><path d="m12 3 8 3v5c0 5-3.4 8.2-8 10-4.6-1.8-8-5-8-10V6l8-3Z" stroke="currentColor" stroke-width="1.7"/><path d="m8.5 12 2.2 2.2 4.8-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                    <h2 class="font-display text-headline-md font-semibold text-slate-900">Administración protegida</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Inicia sesión para acceder a tu espacio de trabajo.</p>
                    <a href="{{ route('login') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-secondary hover:underline">Iniciar sesión <span aria-hidden="true">→</span></a>
                @endauth
            </div>
        </div>
    </section>
@endsection

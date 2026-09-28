<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $seoTitle = trim($__env->yieldContent('title')) ?: ($seoSettings?->default_title ?? config('app.name'));
        $seoDescription = trim($__env->yieldContent('meta_description')) ?: ($seoSettings?->default_description ?? '');
        $seoTemplate = $seoSettings?->title_template ?? '%s | '.config('app.name');
        $canonicalBase = rtrim($seoSettings?->canonical_base_url ?: config('app.url'), '/');
        $canonicalUrl = $canonicalBase.'/'.ltrim(request()->path() === '/' ? '' : request()->path(), '/');
    @endphp
    <title>{{ sprintf($seoTemplate, $seoTitle) }}</title>
    @if($seoDescription)<meta name="description" content="{{ $seoDescription }}">@endif
    <meta name="robots" content="{{ $seoSettings?->robots_directive ?? 'index,follow' }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="{{ $seoSettings?->site_name ?? config('app.name') }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    @if($seoDescription)<meta property="og:description" content="{{ $seoDescription }}">@endif
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($seoSettings?->og_image_path)<meta property="og:image" content="{{ asset(Storage::disk('public')->url($seoSettings->og_image_path)) }}">@endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-surface text-on-surface antialiased">
    <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 font-display text-sm font-semibold tracking-tight text-slate-900 sm:text-lg">
                <span class="grid h-9 w-9 place-items-center rounded-md bg-primary-container text-white" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M12 2.75 20 7.3v9.4L12 21.25 4 16.7V7.3l8-4.55Z" stroke="currentColor" stroke-width="1.7"/><path d="m8 9.2 4-2.3 4 2.3v5.6l-4 2.3-4-2.3V9.2Z" stroke="currentColor" stroke-width="1.5"/><path d="m8 9.2 4 2.3 4-2.3M12 11.5v5.6" stroke="currentColor" stroke-width="1.3"/></svg></span>
                USF Tech Solutions
            </a>
            <nav aria-label="Navegación principal" class="flex items-center gap-1 sm:gap-2">
                <a href="{{ route('about.public') }}" class="hidden rounded-md px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 md:inline-flex">Nosotros</a>
                <a href="{{ route('posts.public.index') }}" class="hidden rounded-md px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 md:inline-flex">Noticias</a>
                <a href="{{ route('videos.public.index') }}" class="hidden rounded-md px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 lg:inline-flex">Videos</a>
                <a href="{{ route('team.public.index') }}" class="hidden rounded-md px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 lg:inline-flex">Equipo</a>
                <a href="{{ route('contact.create') }}" class="hidden rounded-md px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 sm:inline-flex">Contacto</a>
                @foreach($socialLinks ?? [] as $socialLink)<a href="{{ $socialLink->url }}" target="_blank" rel="noopener noreferrer" class="hidden rounded-md px-2 py-2 text-xs font-medium text-secondary hover:bg-secondary-fixed sm:inline-flex" aria-label="{{ $socialLink->label }}">{{ $socialLink->label }}</a>@endforeach
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900">Administración</a>
                    <form action="{{ route('logout') }}" method="POST">@csrf<x-button type="submit" variant="secondary" size="sm">Cerrar sesión</x-button></form>
                @else
                    <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900">Iniciar sesión</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex flex-1 flex-col">@yield('content')</main>

    <footer class="border-t border-slate-200 bg-white px-4 py-6 text-center text-xs text-slate-500">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 sm:flex-row">
            <span>© {{ date('Y') }} USF Tech Solutions</span>
            <nav aria-label="Redes sociales" class="flex flex-wrap justify-center gap-3">@foreach($socialLinks ?? [] as $socialLink)<a href="{{ $socialLink->url }}" target="_blank" rel="noopener noreferrer" class="font-medium text-slate-600 hover:text-secondary">{{ $socialLink->label }}</a>@endforeach</nav>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'CMS Core'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-surface text-on-surface antialiased">
    <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 font-display text-lg font-semibold tracking-tight text-slate-900">
                <span class="grid h-9 w-9 place-items-center rounded-md bg-primary-container text-white" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M12 2.75 20 7.3v9.4L12 21.25 4 16.7V7.3l8-4.55Z" stroke="currentColor" stroke-width="1.7"/><path d="m8 9.2 4-2.3 4 2.3v5.6l-4 2.3-4-2.3V9.2Z" stroke="currentColor" stroke-width="1.5"/><path d="m8 9.2 4 2.3 4-2.3M12 11.5v5.6" stroke="currentColor" stroke-width="1.3"/></svg></span>
                CMS Core
            </a>
            <nav aria-label="Navegación principal" class="flex items-center gap-2">
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

    <footer class="border-t border-slate-200 bg-white px-4 py-5 text-center text-xs text-slate-500">© {{ date('Y') }} CMS Core</footer>
    @stack('scripts')
</body>
</html>

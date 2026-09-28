<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Acceso administrativo') · CMS Core</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-on-surface antialiased">
    <header class="flex h-16 items-center justify-between border-b border-slate-200/80 bg-white/85 px-5 backdrop-blur sm:px-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 rounded-md font-display text-lg font-semibold tracking-tight text-slate-900">
            <span class="grid h-9 w-9 place-items-center rounded-md bg-primary-container text-white" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M12 2.75 20 7.3v9.4L12 21.25 4 16.7V7.3l8-4.55Z" stroke="currentColor" stroke-width="1.7"/><path d="m8 9.2 4-2.3 4 2.3v5.6l-4 2.3-4-2.3V9.2Z" stroke="currentColor" stroke-width="1.5"/><path d="m8 9.2 4 2.3 4-2.3M12 11.5v5.6" stroke="currentColor" stroke-width="1.3"/></svg>
            </span>
            CMS Core
        </a>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
            <span aria-hidden="true">←</span><span class="hidden sm:inline">Volver al sitio</span><span class="sm:hidden">Inicio</span>
        </a>
    </header>

    <main class="relative isolate flex min-h-[calc(100vh-4rem)] items-center justify-center overflow-hidden px-4 py-10 sm:px-6">
        <div aria-hidden="true" class="pointer-events-none absolute left-1/2 top-1/2 -z-10 h-[28rem] w-[28rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-secondary/10 blur-3xl"></div>
        <div class="w-full max-w-lg">@yield('content')</div>
    </main>

    <footer class="px-4 pb-6 text-center text-xs text-slate-500">Acceso reservado para usuarios autorizados.</footer>
    @stack('scripts')
</body>
</html>

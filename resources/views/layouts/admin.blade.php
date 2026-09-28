<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administración') · CMS Core</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-on-surface antialiased">
    <div class="min-h-screen">
        <button type="button" data-admin-sidebar-backdrop aria-label="Cerrar navegación" class="fixed inset-0 z-40 hidden bg-slate-900/40 lg:hidden"></button>
        <aside data-admin-sidebar class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-[width,transform] duration-200 lg:translate-x-0">
            <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-5">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-md bg-primary-container text-white" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M12 2.75 20 7.3v9.4L12 21.25 4 16.7V7.3l8-4.55Z" stroke="currentColor" stroke-width="1.7"/><path d="m8 9.2 4-2.3 4 2.3v5.6l-4 2.3-4-2.3V9.2Z" stroke="currentColor" stroke-width="1.5"/><path d="m8 9.2 4 2.3 4-2.3M12 11.5v5.6" stroke="currentColor" stroke-width="1.3"/></svg></span>
                <span data-sidebar-label class="whitespace-nowrap font-display text-lg font-semibold tracking-tight text-slate-900">CMS Core</span>
            </div>
            <nav aria-label="Navegación administrativa" class="flex-1 px-3 py-5"></nav>
            <div class="border-t border-slate-200 p-3">
                <button type="button" data-admin-sidebar-toggle aria-label="Contraer navegación" aria-expanded="true" class="hidden h-10 w-full items-center justify-center rounded-md text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 lg:flex">
                    <svg data-sidebar-chevron viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </aside>

        <div data-admin-content class="min-h-screen transition-[margin] duration-200 lg:ml-72">
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" data-admin-sidebar-toggle aria-label="Abrir navegación" aria-expanded="false" class="grid h-10 w-10 place-items-center rounded-md text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 lg:hidden"><svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
                    <span class="hidden font-display text-sm font-semibold text-slate-800 sm:block">@yield('title', 'Administración')</span>
                </div>
                <details class="group relative">
                    <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-2 py-1.5 transition hover:bg-slate-100">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-secondary-fixed text-sm font-semibold text-on-secondary-fixed">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="hidden text-left sm:block"><span class="block max-w-40 truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</span><span class="block max-w-40 truncate text-xs text-slate-500">{{ auth()->user()->email }}</span></span>
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 text-slate-500"><path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </summary>
                    <div class="absolute right-0 z-50 mt-2 w-56 rounded-lg border border-slate-200 bg-white p-2 shadow-popover">
                        <div class="border-b border-slate-100 px-3 py-2 sm:hidden"><p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p><p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p></div>
                        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm font-medium text-rose-700 transition hover:bg-rose-50"><svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M10 17l5-5-5-5m5 5H3m9-9h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>Cerrar sesión</button></form>
                    </div>
                </details>
            </header>
            <main class="mx-auto w-full max-w-[1600px] p-4 sm:p-6 lg:p-8">@yield('content')</main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

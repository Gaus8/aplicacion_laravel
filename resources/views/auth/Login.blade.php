@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
    <section class="rounded-xl border border-slate-200/80 bg-white px-6 py-8 shadow-dialog sm:px-10 sm:py-10">
        <div class="mb-7 text-center">
            <span aria-hidden="true" class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-xl bg-surface-container text-primary-container ring-1 ring-slate-200">
                <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7"><path d="M12 2.75 20 7.3v9.4L12 21.25 4 16.7V7.3l8-4.55Z" stroke="currentColor" stroke-width="1.7"/><path d="m8 9.2 4-2.3 4 2.3v5.6l-4 2.3-4-2.3V9.2Z" stroke="currentColor" stroke-width="1.5"/><path d="m8 9.2 4 2.3 4-2.3M12 11.5v5.6" stroke="currentColor" stroke-width="1.3"/></svg>
            </span>
            <x-badge variant="primary" size="sm">Acceso administrativo</x-badge>
            <h1 class="mt-3 font-display text-headline-lg font-semibold tracking-tight text-slate-900">Iniciar sesión</h1>
            <p class="mt-1 text-sm text-slate-600">Accede al panel administrativo de CMS Core.</p>
        </div>

        @if(session('success'))
            <x-alert class="mb-5" variant="success" title="Listo">{{ session('success') }}</x-alert>
        @endif

        @if($errors->any())
            <x-alert class="mb-5" variant="danger" title="No fue posible iniciar sesión">Verifica tu correo y contraseña e inténtalo nuevamente.</x-alert>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            <x-input label="Correo electrónico" name="email" type="email" value="{{ old('email') }}" placeholder="nombre@ejemplo.com" autocomplete="username" required autofocus />

            <x-input label="Contraseña" name="password" id="password" type="password" placeholder="Ingresa tu contraseña" autocomplete="current-password" class="pr-12" required>
                <button type="button" data-password-toggle="password" aria-label="Mostrar contraseña" aria-pressed="false" class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-slate-500 transition hover:text-slate-900">
                    <svg data-password-eye viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg>
                </button>
            </x-input>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <x-checkbox label="Recordarme en este equipo" name="remember" value="1" @if(old('remember')) checked @endif />
                <a href="#recuperar-contrasena" class="text-sm font-medium text-secondary underline-offset-4 transition hover:text-secondary-container hover:underline">¿Olvidaste tu contraseña?</a>
            </div>

            <x-button type="submit" size="lg" class="w-full">Ingresar <span aria-hidden="true">→</span></x-button>
        </form>

        <div class="mt-7 border-t border-slate-200 pt-5 text-center">
            <p class="text-xs text-slate-500">El acceso está disponible para cuentas autorizadas.</p>
        </div>
    </section>
@endsection

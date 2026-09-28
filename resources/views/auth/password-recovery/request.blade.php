@extends('layouts.auth')

@section('title', 'Recuperar contraseña')

@section('content')
<x-card class="px-6 py-8 shadow-dialog sm:px-9 sm:py-10">
    <div class="mb-7 text-center">
        <span aria-hidden="true" class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-xl bg-surface-container text-primary-container ring-1 ring-slate-200">
            <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7"><path d="M12 3v3m0 12v3M3 12h3m12 0h3M5.64 5.64l2.12 2.12m8.48 8.48 2.12 2.12m0-12.72-2.12 2.12m-8.48 8.48-2.12 2.12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.7"/></svg>
        </span>
        <x-badge variant="primary" size="sm">Recuperación de acceso</x-badge>
        <h1 class="mt-3 font-display text-headline-lg font-semibold tracking-tight text-slate-900">Restablece tu contraseña</h1>
        <p class="mt-2 text-sm text-slate-600">Te enviaremos un código temporal al correo asociado a tu cuenta.</p>
    </div>

    <ol aria-label="Pasos para recuperar el acceso" class="mb-7 grid grid-cols-3 text-center text-xs">
        <li class="border-b-2 border-secondary pb-2 font-semibold text-secondary">1. Correo</li>
        <li class="border-b-2 border-slate-200 pb-2 text-slate-500">2. Código</li>
        <li class="border-b-2 border-slate-200 pb-2 text-slate-500">3. Contraseña</li>
    </ol>

    @if($errors->any()) <x-alert class="mb-5" variant="danger">Ingresa un correo válido e inténtalo de nuevo.</x-alert> @endif

    <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
        @csrf
        <x-input label="Correo electrónico" name="email" type="email" value="{{ old('email') }}" placeholder="nombre@ejemplo.com" autocomplete="username" required autofocus :error="$errors->first('email')" />
        <x-button type="submit" size="lg" class="w-full">Enviar código <span aria-hidden="true">→</span></x-button>
    </form>

    <div class="mt-7 border-t border-slate-200 pt-5 text-center">
        <a href="{{ route('login') }}" class="text-sm font-medium text-secondary underline-offset-4 hover:underline">← Volver al inicio de sesión</a>
    </div>
</x-card>
@endsection

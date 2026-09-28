@extends('layouts.auth')

@section('title', 'Verificar código')

@section('content')
<x-card class="px-6 py-8 shadow-dialog sm:px-9 sm:py-10">
    <div class="mb-7 text-center">
        <span aria-hidden="true" class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-xl bg-surface-container text-secondary ring-1 ring-slate-200">
            <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <x-badge variant="primary" size="sm">Verificación de correo</x-badge>
        <h1 class="mt-3 font-display text-headline-lg font-semibold tracking-tight text-slate-900">Ingresa tu código</h1>
        <p class="mt-2 text-sm text-slate-600">Si existe una cuenta para este correo, enviamos un código de 6 dígitos a <span class="font-medium text-slate-800">{{ $maskedEmail }}</span>.</p>
    </div>

    <ol aria-label="Pasos para recuperar el acceso" class="mb-7 grid grid-cols-3 text-center text-xs">
        <li class="border-b-2 border-secondary pb-2 font-semibold text-secondary">1. Correo · Listo</li>
        <li class="border-b-2 border-secondary pb-2 font-semibold text-secondary">2. Código</li>
        <li class="border-b-2 border-slate-200 pb-2 text-slate-500">3. Contraseña</li>
    </ol>

    @if(session('status')) <x-alert class="mb-5" variant="info">{{ session('status') }}</x-alert> @endif
    @if($errors->any()) <x-alert class="mb-5" variant="danger">{{ $errors->first('code') ?? 'El código no es válido o ya venció. Solicita otro.' }}</x-alert> @endif

    <form action="{{ route('password.otp.verify') }}" method="POST" class="space-y-5">
        @csrf
        <x-input label="Código de 6 dígitos" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="••••••" class="text-center font-mono text-xl tracking-[0.55em]" required autofocus :error="$errors->first('code')" />
        <p class="-mt-2 text-xs text-slate-500">El código vence después de 5 minutos y permite hasta 5 intentos.</p>
        <x-button type="submit" size="lg" class="w-full">Verificar código <span aria-hidden="true">→</span></x-button>
    </form>

    <div class="mt-5 flex flex-col items-center justify-between gap-3 rounded-md bg-surface-container-low p-4 sm:flex-row">
        <span class="text-sm text-slate-600">¿No recibiste el correo?</span>
        <form action="{{ route('password.otp.resend') }}" method="POST">@csrf<button type="submit" class="text-sm font-semibold text-secondary underline-offset-4 hover:underline">Reenviar código</button></form>
    </div>
    <div class="mt-6 text-center">
        <a href="{{ route('password.request') }}" class="text-sm font-medium text-slate-600 underline-offset-4 hover:text-secondary hover:underline">← Cambiar correo</a>
    </div>
</x-card>
@endsection

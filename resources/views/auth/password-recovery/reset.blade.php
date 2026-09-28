@extends('layouts.auth')

@section('title', 'Crear contraseña nueva')

@section('content')
<x-card class="px-6 py-8 shadow-dialog sm:px-9 sm:py-10">
    <div class="mb-7 text-center">
        <span aria-hidden="true" class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-xl bg-surface-container text-secondary ring-1 ring-slate-200">
            <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7"><rect x="4" y="10" width="16" height="11" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 5v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        </span>
        <x-badge variant="primary" size="sm">Código verificado</x-badge>
        <h1 class="mt-3 font-display text-headline-lg font-semibold tracking-tight text-slate-900">Crea una contraseña nueva</h1>
        <p class="mt-2 text-sm text-slate-600">Elige una contraseña segura de al menos 8 caracteres.</p>
    </div>

    <ol aria-label="Pasos para recuperar el acceso" class="mb-7 grid grid-cols-3 text-center text-xs">
        <li class="border-b-2 border-secondary pb-2 font-semibold text-secondary">1. Correo · Listo</li>
        <li class="border-b-2 border-secondary pb-2 font-semibold text-secondary">2. Código · Listo</li>
        <li class="border-b-2 border-secondary pb-2 font-semibold text-secondary">3. Contraseña</li>
    </ol>

    @if($errors->any()) <x-alert class="mb-5" variant="danger">Revisa las contraseñas e inténtalo de nuevo. Deben coincidir y tener al menos 8 caracteres.</x-alert> @endif

    <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')
        <x-input label="Nueva contraseña" name="password" type="password" autocomplete="new-password" required :error="$errors->first('password')" />
        <x-input label="Confirmar contraseña" name="password_confirmation" type="password" autocomplete="new-password" required :error="$errors->first('password_confirmation')" />
        <x-button type="submit" size="lg" class="w-full">Guardar contraseña <span aria-hidden="true">✓</span></x-button>
    </form>
</x-card>
@endsection

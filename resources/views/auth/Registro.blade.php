@extends('layouts.auth')

@section('title', 'Crear cuenta')

@section('content')
    <section class="rounded-xl border border-slate-200/80 bg-white px-6 py-8 shadow-dialog sm:px-10 sm:py-10">
        <div class="mb-7 text-center">
            <span aria-hidden="true" class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-xl bg-surface-container text-primary-container ring-1 ring-slate-200">
                <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.7"/><path d="M4 20a8 8 0 0 1 16 0m-2-12h4m-2-2v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            </span>
            <x-badge variant="primary" size="sm">Registro de usuario</x-badge>
            <h1 class="mt-3 font-display text-headline-lg font-semibold tracking-tight text-slate-900">Crear una cuenta</h1>
            <p class="mt-1 text-sm text-slate-600">Completa tus datos para registrarte.</p>
        </div>

        @if($errors->any())
            <x-alert class="mb-5" variant="danger" title="Revisa los datos del formulario">Corrige los campos señalados y vuelve a intentarlo.</x-alert>
        @endif

        <form action="{{ route('registro.store') }}" method="POST" class="space-y-5">
            @csrf
            <x-input label="Nombre completo" name="name" type="text" value="{{ old('name') }}" autocomplete="name" placeholder="Tu nombre" maxlength="255" required :error="$errors->first('name')" />
            <x-input label="Correo electrónico" name="email" type="email" value="{{ old('email') }}" autocomplete="email" placeholder="nombre@ejemplo.com" required :error="$errors->first('email')" />

            <x-input label="Contraseña" name="password" id="register-password" type="password" autocomplete="new-password" placeholder="Mínimo 8 caracteres" class="pr-12" required :error="$errors->first('password')">
                <button type="button" data-password-toggle="register-password" aria-label="Mostrar contraseña" aria-pressed="false" class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-slate-500 transition hover:text-slate-900">
                    <svg data-password-eye viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg>
                </button>
            </x-input>

            <x-input label="Confirmar contraseña" name="password_confirmation" id="register-password-confirmation" type="password" autocomplete="new-password" placeholder="Escribe nuevamente tu contraseña" class="pr-12" required :error="$errors->first('password_confirmation')">
                <button type="button" data-password-toggle="register-password-confirmation" aria-label="Mostrar confirmación de contraseña" aria-pressed="false" class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-slate-500 transition hover:text-slate-900">
                    <svg data-password-eye viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg>
                </button>
            </x-input>

            <x-button type="submit" size="lg" class="w-full">Crear cuenta <span aria-hidden="true">→</span></x-button>
        </form>

        <div class="mt-7 border-t border-slate-200 pt-5 text-center">
            <p class="text-sm text-slate-600">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="font-semibold text-secondary underline-offset-4 hover:underline">Inicia sesión</a></p>
            <p class="mt-3 text-xs text-slate-500">El registro no concede permisos administrativos automáticamente.</p>
        </div>
    </section>
@endsection

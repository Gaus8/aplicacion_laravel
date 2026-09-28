@extends('layouts.admin')

@section('title', 'Configuración SMTP')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <x-breadcrumb :items="[['label' => 'Administración', 'url' => route('dashboard')], ['label' => 'Configuración SMTP']]" />
    <header>
        <h1 class="font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Configuración de correo</h1>
        <p class="mt-2 text-sm text-slate-600">Configura el servidor SMTP que utiliza la plataforma para enviar correos.</p>
    </header>

    @if(session('success')) <x-alert variant="success" class="mb-4">{{ session('success') }}</x-alert> @endif
    @if(session('error') || $errors->has('smtp')) <x-alert variant="danger" class="mb-4">{{ session('error') ?? $errors->first('smtp') }}</x-alert> @endif
    @if($errors->any() && !$errors->has('smtp') && !$errors->has('password')) <x-alert variant="danger" class="mb-4">Revisa los campos marcados e inténtalo de nuevo.</x-alert> @endif
    @if($errors->has('password')) <x-alert variant="danger" class="mb-4">{{ $errors->first('password') }}</x-alert> @endif

    <x-card title="Servidor SMTP" description="Los cambios se aplicarán a los siguientes correos enviados desde la plataforma.">
        <form action="{{ route('admin.smtp.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid gap-5 sm:grid-cols-2">
                <x-input label="Servidor SMTP" name="host" value="{{ old('host', $setting?->host) }}" placeholder="smtp.ejemplo.com" required autocomplete="off" />
                <x-input label="Puerto" name="port" type="number" min="1" max="65535" value="{{ old('port', $setting?->port ?? 587) }}" required />
                <x-select label="Cifrado" name="encryption">
                    <option value="">Sin cifrado explícito</option>
                    <option value="tls" @selected(old('encryption', $setting?->encryption) === 'tls')>TLS / STARTTLS</option>
                    <option value="ssl" @selected(old('encryption', $setting?->encryption) === 'ssl')>SSL</option>
                </x-select>
                <div class="flex items-end pb-1">
                    <input type="hidden" name="authentication_required" value="0">
                    <x-checkbox name="authentication_required" value="1" :checked="(bool) old('authentication_required', $setting?->authentication_required ?? true)" label="El servidor requiere autenticación" />
                </div>
                <x-input label="Usuario SMTP" name="username" value="{{ old('username', $setting?->username) }}" autocomplete="username" />
                <x-input label="Contraseña SMTP" name="password" type="password" autocomplete="new-password" placeholder="{{ $setting?->password_encrypted ? 'Deja vacío para conservar la guardada' : 'Contraseña SMTP' }}" />
                <x-input label="Correo remitente" name="from_address" type="email" value="{{ old('from_address', $setting?->from_address) }}" placeholder="notificaciones@ejemplo.com" required />
                <x-input label="Nombre remitente" name="from_name" value="{{ old('from_name', $setting?->from_name) }}" placeholder="Nombre del sitio" required />
            </div>
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-5">
                <div>
                    <input type="hidden" name="is_active" value="0">
                    <x-checkbox name="is_active" value="1" :checked="(bool) old('is_active', $setting?->is_active ?? false)" label="Activar esta configuración para los envíos" />
                    @if($setting?->last_tested_at)
                        <p class="mt-2 text-xs text-slate-500">Última prueba: {{ $setting->last_tested_at->format('d/m/Y H:i') }} · {{ $setting->last_test_status === 'success' ? 'Correcta' : 'Fallida' }}</p>
                    @endif
                </div>
                <x-button type="submit">Guardar configuración</x-button>
            </div>
        </form>
    </x-card>

    <x-card title="Probar conexión" description="Enviaremos un mensaje de prueba a {{ auth()->user()->email }}.">
        <form action="{{ route('admin.smtp.test') }}" method="POST" class="flex justify-end">
            @csrf
            <x-button type="submit" variant="secondary" :disabled="!($setting?->exists ?? false)">Enviar correo de prueba</x-button>
        </form>
    </x-card>
</div>
@endsection

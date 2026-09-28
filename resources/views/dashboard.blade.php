@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6 sm:space-y-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <x-breadcrumb :items="[['label' => 'Administración'], ['label' => 'Dashboard']]" class="mb-3" />
            <h1 class="font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Hola, {{ auth()->user()->name }}</h1>
            <p class="mt-2 text-sm text-slate-600">Resumen de cuentas, contenido y actividad reciente del CMS.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.media.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50">Abrir Multimedia</a>
            <a href="{{ route('admin.media.emails') }}" class="inline-flex items-center justify-center rounded-md bg-secondary px-3.5 py-2 text-sm font-medium text-on-secondary shadow-sm transition hover:bg-secondary-container">Enviar correo</a>
        </div>
    </header>

    <section aria-label="Indicadores del sistema" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-card padding="md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Cuentas registradas</p>
            <p class="mt-3 font-display text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($stats['users']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Usuarios almacenados</p>
        </x-card>
        <x-card padding="md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Archivos multimedia</p>
            <p class="mt-3 font-display text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($stats['media']) }}</p>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($stats['media_week']) }} cargados en los últimos 7 días</p>
        </x-card>
        <x-card padding="md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Correos enviados este mes</p>
            <p class="mt-3 font-display text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($stats['emails_month']) }}</p>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($stats['emails_today']) }} enviados hoy</p>
        </x-card>
        <x-card padding="md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Accesos fallidos hoy</p>
            <p class="mt-3 font-display text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($stats['failed_logins_today']) }}</p>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($stats['audit_today']) }} eventos registrados hoy</p>
        </x-card>
    </section>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(19rem,1fr)]">
        <x-card title="Actividad auditada" description="Eventos registrados durante los últimos 7 días.">
            <div role="img" aria-label="Cantidad diaria de eventos de auditoría en los últimos siete días" class="mt-6 grid h-48 grid-cols-7 items-end gap-3 border-b border-slate-200 pb-2 sm:gap-5">
                @foreach($activitySeries as $day)
                    <div class="flex h-full flex-col items-center justify-end gap-2">
                        <span class="text-xs tabular-nums text-slate-500">{{ $day['count'] }}</span>
                        <div class="flex h-32 w-full items-end justify-center rounded-t bg-surface-container-low">
                            <span class="w-full max-w-10 rounded-t bg-secondary" style="height: {{ $day['height'] }}%" title="{{ $day['count'] }} eventos"></span>
                        </div>
                        <span class="text-xs capitalize text-slate-500">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card title="Accesos directos" description="Ir a las herramientas disponibles.">
            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                <a href="{{ route('admin.media.index') }}" class="flex items-center justify-between rounded-md bg-surface-container-low px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-indigo-50 hover:text-secondary"><span>Galería multimedia</span><span aria-hidden="true">→</span></a>
                <a href="{{ route('admin.media.subirImagen') }}" class="flex items-center justify-between rounded-md bg-surface-container-low px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-indigo-50 hover:text-secondary"><span>Cargar imagen</span><span aria-hidden="true">→</span></a>
                <a href="{{ route('admin.media.emails') }}" class="flex items-center justify-between rounded-md bg-surface-container-low px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-indigo-50 hover:text-secondary"><span>Enviar correo</span><span aria-hidden="true">→</span></a>
                <a href="{{ route('admin.audit.index') }}" class="flex items-center justify-between rounded-md bg-surface-container-low px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-indigo-50 hover:text-secondary"><span>Registro de auditoría</span><span aria-hidden="true">→</span></a>
            </div>
        </x-card>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <x-card title="Actividad reciente" description="Últimas acciones registradas en el sistema." padding="none">
            <x-slot:actions><a href="{{ route('admin.audit.index') }}" class="text-sm font-medium text-secondary hover:underline">Ver auditoría →</a></x-slot:actions>
            @if($recentActivity->isNotEmpty())
                @php
                    $eventLabels = [
                        'auth.login.succeeded' => 'Inició sesión',
                        'auth.login.failed' => 'Intento de acceso fallido',
                        'auth.logout' => 'Cerró sesión',
                        'auth.password_recovery.requested' => 'Solicitó recuperación de contraseña',
                        'auth.password_recovery.resent' => 'Solicitó reenviar código',
                        'auth.password_recovery.code_rejected' => 'Código de recuperación rechazado',
                        'auth.password_recovery.code_verified' => 'Verificó código de recuperación',
                        'auth.password_reset.completed' => 'Restableció su contraseña',
                        'smtp.settings.updated' => 'Actualizó la configuración SMTP',
                        'smtp.connection.tested' => 'Probó la conexión SMTP',
                        'media.uploaded' => 'Cargó un elemento multimedia',
                        'media.deleted' => 'Eliminó un elemento multimedia',
                        'email.sent' => 'Envió un correo',
                    ];
                    $statusVariants = ['success' => 'success', 'failure' => 'danger', 'warning' => 'warning', 'blocked' => 'danger'];
                @endphp
                <ul class="divide-y divide-slate-100 border-t border-slate-100">
                    @foreach($recentActivity as $entry)
                        <li class="flex items-start justify-between gap-3 px-5 py-4">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-800">{{ $entry->actor?->name ?? $entry->actor_name ?? 'Visitante' }} <span class="font-normal text-slate-600">{{ $eventLabels[$entry->event] ?? $entry->event }}</span></p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ $entry->subject ?? '—' }} · {{ $entry->created_at?->format('d/m/Y H:i') }}</p>
                            </div>
                            <x-badge :variant="$statusVariants[$entry->status] ?? 'neutral'">{{ $entry->status === 'success' ? 'Correcto' : ($entry->status === 'failure' ? 'Fallido' : ucfirst($entry->status)) }}</x-badge>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="border-t border-slate-100 p-5"><x-empty-state title="Sin actividad registrada" description="Las acciones del sistema aparecerán aquí." /></div>
            @endif
        </x-card>

        <x-card title="Archivos recientes" description="Últimos elementos de Multimedia cargados." padding="none">
            <x-slot:actions><a href="{{ route('admin.media.index') }}" class="text-sm font-medium text-secondary hover:underline">Ver galería →</a></x-slot:actions>
            @if($recentMedia->isNotEmpty())
                <ul class="divide-y divide-slate-100 border-t border-slate-100">
                    @foreach($recentMedia as $item)
                        <li class="flex items-center gap-3 px-5 py-3">
                            <a href="{{ route('admin.media.file', $item) }}" target="_blank" rel="noopener noreferrer" class="h-12 w-16 shrink-0 overflow-hidden rounded bg-surface-container" aria-label="Abrir {{ $item->name }}"><img src="{{ route('admin.media.file', $item) }}" alt="" class="h-full w-full object-cover"></a>
                            <div class="min-w-0 flex-1"><p class="truncate text-sm font-medium text-slate-800">{{ $item->name }}</p><p class="text-xs text-slate-500">{{ $item->created_at?->format('d/m/Y H:i') }}</p></div>
                            <x-badge>{{ strtoupper(str_replace('image/', '', (string) $item->mime_type)) ?: 'ARCHIVO' }}</x-badge>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="border-t border-slate-100 p-5"><x-empty-state title="No hay archivos todavía" description="Los archivos cargados aparecerán aquí."><x-slot:action><a href="{{ route('admin.media.subirImagen') }}" class="text-sm font-medium text-secondary hover:underline">Cargar imagen</a></x-slot:action></x-empty-state></div>
            @endif
        </x-card>
    </section>

    <x-card title="Correos enviados recientemente" description="Mensajes registrados tras un envío correcto." padding="none">
        @if($recentEmails->isNotEmpty())
            <x-table caption="Correos enviados recientemente" class="rounded-none border-0 border-t border-slate-100 shadow-none">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500"><tr><th scope="col" class="px-5 py-3 font-semibold">Destinatario</th><th scope="col" class="px-5 py-3 font-semibold">Asunto</th><th scope="col" class="px-5 py-3 font-semibold">Fecha</th><th scope="col" class="px-5 py-3 font-semibold">Estado</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentEmails as $email)
                        <tr><td class="px-5 py-4 text-sm text-slate-700">{{ $email->recipient }}</td><td class="max-w-72 truncate px-5 py-4 text-sm text-slate-700">{{ $email->subject }}</td><td class="whitespace-nowrap px-5 py-4 text-sm tabular-nums text-slate-500">{{ $email->created_at?->format('d/m/Y H:i') }}</td><td class="px-5 py-4"><x-badge variant="success">Enviado</x-badge></td></tr>
                    @endforeach
                </tbody>
            </x-table>
        @else
            <div class="border-t border-slate-100 p-5"><x-empty-state title="No hay correos registrados" description="Los mensajes enviados aparecerán aquí." /></div>
        @endif
    </x-card>
</div>
@endsection

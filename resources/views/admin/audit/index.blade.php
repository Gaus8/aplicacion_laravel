@extends('layouts.admin')

@section('title', 'Auditoría')

@section('content')
<div class="space-y-6">
    <x-breadcrumb :items="[['label' => 'Administración', 'url' => route('dashboard')], ['label' => 'Seguridad y auditoría']]" />
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Registro de auditoría</h1>
            <p class="mt-2 text-sm text-slate-600">Historial de accesos y cambios registrados por el sistema.</p>
        </div>
        <x-badge variant="primary">{{ number_format($logs->total()) }} eventos</x-badge>
    </header>

    <x-card padding="sm">
        <form method="GET" action="{{ route('admin.audit.index') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_12rem_auto] sm:items-end">
            <x-input label="Buscar" name="search" value="{{ $search }}" placeholder="Usuario, evento o dirección IP" />
            <x-select label="Estado" name="status">
                <option value="">Todos los estados</option>
                <option value="success" @selected($status === 'success')>Correcto</option>
                <option value="failure" @selected($status === 'failure')>Fallido</option>
                <option value="warning" @selected($status === 'warning')>Advertencia</option>
                <option value="blocked" @selected($status === 'blocked')>Bloqueado</option>
            </x-select>
            <div class="flex gap-2">
                <x-button type="submit">Filtrar</x-button>
                @if($search !== '' || $status !== '')<a href="{{ route('admin.audit.index') }}" class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Limpiar</a>@endif
            </div>
        </form>
    </x-card>

    <x-table caption="Eventos de auditoría registrados">
        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
            <tr>
                <th scope="col" class="px-4 py-3">Usuario / sujeto</th>
                <th scope="col" class="px-4 py-3">Evento</th>
                <th scope="col" class="px-4 py-3">Dirección IP</th>
                <th scope="col" class="px-4 py-3">Agente de usuario</th>
                <th scope="col" class="px-4 py-3">Fecha y hora</th>
                <th scope="col" class="px-4 py-3">Estado</th>
                <th scope="col" class="px-4 py-3">Detalles</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($logs as $log)
                @php
                    $eventLabels = [
                        'auth.login.succeeded' => 'Inicio de sesión correcto',
                        'auth.login.failed' => 'Intento de inicio de sesión fallido',
                        'auth.logout' => 'Cierre de sesión',
                        'auth.password_recovery.requested' => 'Solicitud de recuperación de contraseña',
                        'auth.password_recovery.resent' => 'Solicitud de reenvío de código',
                        'auth.password_recovery.code_rejected' => 'Código de recuperación rechazado',
                        'auth.password_recovery.code_verified' => 'Código de recuperación verificado',
                        'auth.password_reset.completed' => 'Contraseña restablecida',
                        'smtp.settings.updated' => 'Configuración SMTP actualizada',
                        'smtp.connection.tested' => 'Prueba de conexión SMTP',
                        'media.uploaded' => 'Elemento multimedia cargado',
                        'media.deleted' => 'Elemento multimedia eliminado',
                        'email.sent' => 'Correo enviado',
                    ];
                    $statusVariants = ['success' => 'success', 'failure' => 'danger', 'warning' => 'warning', 'blocked' => 'danger'];
                @endphp
                <tr class="align-top">
                    <td class="px-4 py-3"><p class="font-medium text-slate-900">{{ $log->actor?->name ?? $log->actor_name ?? 'Visitante' }}</p>@if($log->subject)<p class="mt-0.5 text-xs text-slate-500">{{ $log->subject }}</p>@endif</td>
                    <td class="max-w-64 px-4 py-3 text-slate-700">{{ $eventLabels[$log->event] ?? $log->event }}</td>
                    <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-600">{{ $log->ip_address ?? '—' }}</td>
                    <td class="max-w-56 px-4 py-3 text-xs text-slate-600" title="{{ $log->user_agent }}">{{ $log->user_agent ? \Illuminate\Support\Str::limit($log->user_agent, 64) : '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-xs text-slate-600">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                    <td class="px-4 py-3"><x-badge :variant="$statusVariants[$log->status] ?? 'neutral'">{{ $log->status === 'success' ? 'Correcto' : ($log->status === 'failure' ? 'Fallido' : ucfirst($log->status)) }}</x-badge></td>
                    <td class="px-4 py-3 text-xs">
                        @if($log->metadata)<details><summary class="cursor-pointer font-medium text-secondary hover:underline">Ver detalles</summary><pre class="mt-2 max-w-64 overflow-auto rounded bg-slate-50 p-2 text-[11px] text-slate-700">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></details>@else<span class="text-slate-400">—</span>@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-slate-500">No hay eventos que coincidan con estos filtros.</td></tr>
            @endforelse
        </tbody>
    </x-table>

    <div>{{ $logs->links() }}</div>
</div>
@endsection

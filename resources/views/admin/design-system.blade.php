<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Design system | CMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <header class="mb-10 flex flex-col justify-between gap-5 border-b border-slate-200 pb-8 sm:flex-row sm:items-end">
            <div>
                <x-breadcrumb :items="[['label' => 'Administración', 'url' => route('dashboard')], ['label' => 'Design system']]" class="mb-4" />
                <p class="mb-2 text-label-sm font-semibold uppercase tracking-wider text-secondary">Modern Enterprise CMS</p>
                <h1 class="font-display text-headline-xl font-bold tracking-tight text-slate-900">Sistema de diseño</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">Tokens y componentes de interfaz para las vistas administrativas.</p>
            </div>
            <x-badge variant="success">Tailwind CSS 4</x-badge>
        </header>

        <main class="space-y-8">
            <section aria-label="Tokens visuales" class="grid gap-6 lg:grid-cols-2">
                <x-card title="Colores" description="Paleta de superficies, marca y estados." padding="md">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach([['Canvas','bg-surface','surface'],['Primario','bg-primary','primary'],['Acción','bg-secondary','secondary'],['Éxito','bg-success','success'],['Advertencia','bg-warning','warning'],['Error','bg-danger','danger']] as [$label,$swatch,$token])
                            <div class="overflow-hidden rounded-md border border-slate-200"><div class="h-12 {{ $swatch }}"></div><div class="px-3 py-2"><p class="text-xs font-medium">{{ $label }}</p><code class="font-mono text-[10px] text-slate-500">{{ $token }}</code></div></div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2"><span class="rounded-md bg-surface-container px-3 py-2 text-xs">surface-container</span><span class="rounded-md bg-slate-100 px-3 py-2 text-xs">slate-100</span><span class="rounded-md bg-primary-container px-3 py-2 text-xs text-white">primary-container</span></div>
                </x-card>
                <x-card title="Tipografía" description="Plus Jakarta Sans para encabezados; Inter para interfaz." padding="md">
                    <div class="space-y-3"><p class="font-display text-headline-xl font-bold">Encabezado XL</p><p class="font-display text-headline-lg font-semibold">Encabezado LG</p><p class="font-display text-headline-md font-semibold">Encabezado MD</p><p class="text-body-lg">Texto de cuerpo grande para lectura.</p><p class="text-body-md text-slate-600">Texto base y descripción secundaria.</p><p class="text-body-sm text-slate-500">Metadatos en tamaño pequeño.</p><p class="font-mono text-code-sm">const status = "published";</p></div>
                </x-card>
                <x-card title="Radios" description="Formas sutiles para controles, paneles y estados." padding="md">
                    <div class="flex flex-wrap items-end gap-5">@foreach([['sm','rounded-sm'],['base','rounded'],['md','rounded-md'],['lg','rounded-lg'],['xl','rounded-xl'],['full','rounded-full']] as [$name,$class])<div class="text-center"><div class="mb-2 h-12 w-16 border-2 border-secondary bg-indigo-50 {{ $class }}"></div><code class="text-xs text-slate-500">{{ $name }}</code></div>@endforeach</div>
                </x-card>
                <x-card title="Sombras" description="Niveles de elevación usados en superficies flotantes." padding="md">
                    <div class="grid grid-cols-2 gap-4"><div class="rounded-lg border border-slate-200 bg-white p-4 shadow-card">Tarjeta <code class="block text-xs text-slate-500">shadow-card</code></div><div class="rounded-lg border border-slate-200 bg-white p-4 shadow-popover">Menú <code class="block text-xs text-slate-500">shadow-popover</code></div><div class="rounded-lg border border-slate-200 bg-white p-4 shadow-dialog">Diálogo <code class="block text-xs text-slate-500">shadow-dialog</code></div><div class="rounded-lg bg-slate-900 p-4 text-white shadow-toast">Toast <code class="block text-xs text-slate-300">shadow-toast</code></div></div>
                </x-card>
            </section>

            <x-card title="Botones" description="Variantes, tamaños y estados deshabilitados."><div class="flex flex-wrap items-center gap-3"><x-button>Primario</x-button><x-button variant="secondary">Secundario</x-button><x-button variant="danger">Destructivo</x-button><x-button variant="ghost">Ghost</x-button><x-button size="sm">Pequeño</x-button><x-button size="lg">Grande</x-button><x-button disabled>Deshabilitado</x-button></div></x-card>

            <section class="grid gap-6 lg:grid-cols-2">
                <x-card title="Campos de formulario" description="Entrada, selección y área de texto con ayuda y validación.">
                    <div class="space-y-4"><x-input label="Nombre" name="demo-name" placeholder="Nombre completo" hint="Este campo es opcional." /><x-input label="Correo electrónico" name="demo-email" type="email" value="correo-invalido" error="Introduce una dirección de correo válida." /><x-input label="Solo lectura" name="demo-readonly" value="Valor bloqueado" readonly /><x-input label="Deshabilitado" name="demo-disabled" value="No disponible" disabled /><x-select label="Estado" name="demo-status"><option value="">Selecciona un estado</option><option>Publicado</option><option>Borrador</option><option>Programado</option></x-select><x-textarea label="Descripción" name="demo-description" rows="3" placeholder="Escribe una descripción..." hint="Hasta 500 caracteres." /><x-textarea label="Campo con error" name="demo-invalid" rows="2" error="La descripción es obligatoria.">Texto de ejemplo</x-textarea></div>
                </x-card>
                <x-card title="Selección" description="Casilla, interruptor y estados seleccionados.">
                    <div class="space-y-5"><x-checkbox label="Acepto los términos" name="terms" checked /><x-checkbox label="Notificaciones por correo" description="Recibe actualizaciones importantes." name="notifications" /><x-checkbox label="Opción deshabilitada" name="disabled-check" disabled /><x-switch label="Publicación activa" description="Visible para los visitantes." name="active" checked /><x-switch label="Modo mantenimiento" name="maintenance" /><x-switch label="Interruptor deshabilitado" name="disabled-switch" disabled /></div>
                </x-card>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <x-card title="Badges" description="Indicadores de estado compactos."><div class="flex flex-wrap gap-2"><x-badge>Neutral</x-badge><x-badge variant="success">Publicado</x-badge><x-badge variant="warning">Pendiente</x-badge><x-badge variant="danger">Error</x-badge><x-badge variant="info">Informativo</x-badge><x-badge variant="primary">Destacado</x-badge><x-badge size="sm">Pequeño</x-badge></div></x-card>
                <x-card title="Alertas" description="Mensajes persistentes con cuatro tonos semánticos."><div class="space-y-3"><x-alert title="Información" variant="info">Hay una nueva versión disponible.</x-alert><x-alert title="Guardado" variant="success">Los cambios se guardaron correctamente.</x-alert><x-alert title="Atención" variant="warning">Revisa los campos antes de continuar.</x-alert><x-alert title="No se pudo completar" variant="danger" dismissible>Inténtalo de nuevo más tarde.</x-alert></div></x-card>
            </section>

            <x-card title="Tabla" description="Filas de muestra, encabezado, etiquetas y paginación." padding="none">
                <x-table caption="Usuarios recientes">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th scope="col" class="px-5 py-3">Usuario</th><th scope="col" class="px-5 py-3">Rol</th><th scope="col" class="px-5 py-3">Estado</th><th scope="col" class="px-5 py-3">Último acceso</th></tr></thead>
                    <tbody class="divide-y divide-slate-100"><tr><th scope="row" class="px-5 py-4 font-medium text-slate-900">Ana Torres</th><td class="px-5 py-4 text-slate-600">Administradora</td><td class="px-5 py-4"><x-badge variant="success">Activo</x-badge></td><td class="px-5 py-4 tabular-nums text-slate-500">Hoy, 09:42</td></tr><tr><th scope="row" class="px-5 py-4 font-medium text-slate-900">Luis Gómez</th><td class="px-5 py-4 text-slate-600">Editor</td><td class="px-5 py-4"><x-badge variant="warning">Invitado</x-badge></td><td class="px-5 py-4 tabular-nums text-slate-500">Ayer, 16:08</td></tr><tr><th scope="row" class="px-5 py-4 font-medium text-slate-900">María Rojas</th><td class="px-5 py-4 text-slate-600">Autor</td><td class="px-5 py-4"><x-badge variant="danger">Suspendido</x-badge></td><td class="px-5 py-4 tabular-nums text-slate-500">22 sep. 2026</td></tr></tbody>
                </x-table>
                <div class="p-5"><x-pagination :current-page="2" :total-pages="5" /></div>
            </x-card>

            <section class="grid gap-6 lg:grid-cols-2">
                <x-card title="Dropdown y pestañas" description="Menú desplegable accesible y navegación por pestañas.">
                    <div class="mb-6"><x-dropdown label="Acciones"><a href="#editar" class="block rounded px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Editar elemento</a><a href="#duplicar" class="block rounded px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Duplicar</a><button type="button" class="block w-full rounded px-3 py-2 text-left text-sm text-rose-700 hover:bg-rose-50">Eliminar</button></x-dropdown></div>
                    <x-tabs id="demo-tabs" :tabs="['overview' => 'Resumen', 'activity' => 'Actividad', 'settings' => 'Configuración']">
                        <section id="demo-tabs-panel-overview" role="tabpanel" aria-labelledby="demo-tabs-tab-overview" class="text-sm text-slate-600">Resumen general del contenido seleccionado.</section>
                        <section id="demo-tabs-panel-activity" role="tabpanel" aria-labelledby="demo-tabs-tab-activity" hidden class="text-sm text-slate-600">Actividad reciente del elemento.</section>
                        <section id="demo-tabs-panel-settings" role="tabpanel" aria-labelledby="demo-tabs-tab-settings" hidden class="text-sm text-slate-600">Preferencias y opciones de configuración.</section>
                    </x-tabs>
                </x-card>
                <x-card title="Modal y confirmación" description="Diálogos nativos con foco y cierre accesible.">
                    <div class="flex flex-wrap gap-3"><x-button data-modal-open="sample-modal">Abrir modal</x-button><x-button variant="danger" data-modal-open="sample-confirm">Abrir confirmación</x-button><x-button variant="secondary" data-toast-open="sample-toast">Mostrar toast</x-button></div>
                    <p class="mt-4 text-xs text-slate-500">Cierra con Escape o con el botón de cierre.</p>
                </x-card>
            </section>

            <x-modal id="sample-modal" title="Detalles del elemento" description="Ejemplo de diálogo informativo."><p class="text-sm text-slate-600">Este modal usa el elemento nativo <code class="font-mono text-xs">dialog</code>, con scrim y sombra de elevación.</p><div class="mt-6 flex justify-end"><x-button variant="secondary" data-modal-close="sample-modal">Cerrar</x-button></div></x-modal>
            <x-confirm-dialog id="sample-confirm" title="¿Eliminar elemento?" description="Esta acción no se puede deshacer.">El elemento de ejemplo se eliminaría permanentemente.</x-confirm-dialog>
            <x-toast id="sample-toast">La operación de muestra terminó correctamente.</x-toast>

            <x-card title="Breadcrumb y estado vacío" description="Orientación contextual y contenido sin resultados.">
                <x-breadcrumb :items="[['label' => 'CMS', 'url' => '#cms'], ['label' => 'Contenido', 'url' => '#contenido'], ['label' => 'Resultados']]" class="mb-6" />
                <x-empty-state title="No hay resultados" description="Prueba con otros términos o ajusta los filtros para encontrar contenido."><x-slot:action><x-button variant="secondary">Limpiar filtros</x-button></x-slot:action></x-empty-state>
            </x-card>
        </main>
        <footer class="mt-10 border-t border-slate-200 pt-5 text-xs text-slate-500">Vista temporal de componentes · Modern Enterprise CMS</footer>
    </div>
</body>
</html>

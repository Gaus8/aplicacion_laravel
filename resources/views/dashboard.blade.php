<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistema Auth</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

    <!-- Navegación Superior -->
    <header class="navbar">
        <div class="navbar-container">
            <div class="brand-wrapper">
                <div class="brand-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 2 2 002-2v-6a2 2 2 00-2-2H6a2 2 2 00-2 2v6a2 2 2 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <span class="brand-title">Sistema Auth</span>
            </div>

            <div class="user-nav">
                <div class="user-profile">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info-header">
                        <span class="user-name-header">{{ auth()->user()->name }}</span>
                        <span class="user-role-header">Usuario autenticado</span>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-danger-sm">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        
        <!-- Header de la Página -->
        <div class="page-header">
            <h1 class="page-title">Panel de Control</h1>
            <p class="page-subtitle">Bienvenido al área protegida de administración.</p>
        </div>

        <!-- Tarjetas KPI / Resumen de Estado -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-data">
                    <span class="kpi-label">Usuario Conectado</span>
                    <span class="kpi-value" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
                </div>
                <div class="kpi-icon kpi-icon-blue">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-data">
                    <span class="kpi-label">Correo Registrado</span>
                    <span class="kpi-value" title="{{ auth()->user()->email }}">{{ auth()->user()->email }}</span>
                </div>
                <div class="kpi-icon kpi-icon-green">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-data">
                    <span class="kpi-label">Archivos en Galería</span>
                    <span class="kpi-value">{{ count($media) }} elementos</span>
                </div>
                <div class="kpi-icon kpi-icon-purple">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Panel Principal de Galería -->
        <section class="gallery-panel">
            <div class="gallery-header">
                <div class="gallery-title-area">
                    <h2>Galería de Archivos</h2>
                    <p>Gestiona e inspecciona tus archivos multimedia cargados.</p>
                </div>
                
                <a href="{{ route('admin.media.subirImagen') }}" class="btn-upload">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Subir Nueva Imagen
                </a>
            </div>

            @if(count($media) > 0)
                <div class="gallery-grid">
                    @foreach($media as $item)
                        <article class="media-card">
                            <a href="{{ Storage::url($item->path) }}" target="_blank" class="media-preview-wrapper">
                                <img src="{{ Storage::url($item->path) }}" alt="{{ $item->name }}" class="media-image">
                            </a>
                            <div class="media-body">
                                <h3 class="media-title" title="{{ $item->name }}">
                                    {{ $item->name }}
                                </h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p>No se encontraron imágenes en la galería.</p>
                </div>
            @endif
        </section>

    </main>

</body>
</html>
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
    <nav class="navbar">
        <div class="navbar-container">
            <span class="brand-title">Sistema Auth</span>
            <div class="user-nav">
                <span class="user-greeting">
                    Hola, <strong>{{ auth()->user()->name }}</strong>
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-danger-sm">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="main-container">
        
        <!-- Tarjeta de Info del Usuario -->
        <div class="card">
            <h1 class="card-title">Panel de Control (Dashboard)</h1>
            <p class="card-subtitle">
                ¡Has iniciado sesión con éxito! Esta es una vista protegida mediante el middleware <code class="badge-code">auth</code>.
            </p>

            <div class="info-grid">
                <div class="info-box info-box-blue">
                    <h3 class="info-label">Nombre del Usuario</h3>
                    <p class="info-value">{{ auth()->user()->name }}</p>
                </div>
                <div class="info-box info-box-green">
                    <h3 class="info-label">Correo Registrado</h3>
                    <p class="info-value">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>

        <!-- Galería del Dashboard -->
        <div class="card">
            <div class="gallery-header">
                <h2 class="gallery-title">Galería del dashboard</h2>
                
                <a href="{{ route('admin.media.subirImagen') }}" class="btn-upload">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Subir Nueva Imagen
                </a>
            </div>

            <div class="gallery-grid">
                @foreach($media as $item)
                    <article class="media-card">
                        <div>
                            <a href="{{ Storage::url($item->path) }}" target="_blank">
                                <img src="{{ Storage::url($item->path) }}" 
                                     alt="{{ $item->name }}" 
                                     class="media-image">
                            </a>
                            
                            <h3 class="media-title" title="{{ $item->name }}">
                                {{ $item->name }}
                            </h3>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

    </main>

</body>
</html>
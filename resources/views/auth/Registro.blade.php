<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Auth System</title>
    <link rel="stylesheet" href="{{ asset('css/registro.css') }}">
</head>
<body>

    <div class="glow-effect"></div>

    <div class="auth-card">
        
        <div class="card-header">
            <div class="brand-badge">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h2 class="card-title">Crear una cuenta</h2>
            <p class="card-subtitle">Completa el formulario para registrarte</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="alert-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('registro') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nombre completo</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Juan Pérez" class="form-input">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="nombre@ejemplo.com" class="form-input">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="••••••••" class="form-input">
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••" class="form-input">
            </div>

            <button type="submit" class="btn-submit">
                Registrarse
            </button>
        </form>

        <p class="card-footer-text">
            ¿Ya tienes cuenta? 
            <a href="{{ route('login') }}" class="auth-link">Inicia sesión aquí</a>
        </p>
    </div>

</body>
</html>
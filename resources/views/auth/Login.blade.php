<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Auth System</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="glow-effect"></div>

    <div class="auth-card">
        
        <div class="card-header">
            <div class="brand-badge">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 2 2 002-2v-6a2 2 2 00-2-2H6a2 2 2 00-2 2v6a2 2 2 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="card-title">Iniciar Sesión</h2>
            <p class="card-subtitle">Ingresa tus credenciales para acceder</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="alert-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="nombre@ejemplo.com" class="form-input">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="••••••••" class="form-input">
            </div>

            <button type="submit" class="btn-submit">
                Ingresar
            </button>
        </form>

        <p class="card-footer-text">
            ¿No tienes cuenta? 
            <a href="{{ route('registro') }}" class="auth-link">Regístrate aquí</a>
        </p>
    </div>

</body>
</html>
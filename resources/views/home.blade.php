<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio | Auth System</title>
</head>
<body>
    <h1>Bienvenido al Sistema de Autenticación</h1>
    
    @auth
        <p>Hola, {{ auth()->user()->name }}</p>
        <a href="{{ route('dashboard') }}">Ir al Dashboard</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Cerrar Sesión</button>
        </form>
    @else
        <a href="{{ route('login') }}">Iniciar Sesión</a> | 
        <a href="{{ route('register') }}">Registrarse</a>
    @endauth
</body>
</html>
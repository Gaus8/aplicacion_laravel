<?php

use App\Http\Controllers\AuthController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
// Vista principal accesible para todos
Route::get('/', function () {
    return view('home');
})->name('home');

// Rutas para usuarios invitados (RedirectIfAuthenticated)
Route::middleware(RedirectIfAuthenticated::class)->group(function () {
    Route::get('/registro', [AuthController::class, 'showRegistro'])->name('registro');
    Route::post('/registro', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // Limitación a 5 intentos por minuto
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

// Rutas protegidas
Route::middleware(Authenticate::class)->group(function () {
    Route::get('/dashboard', function (Request $request) {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Usuario Autenticado. Bienvenido al Dashboard',
                'user' => $request->user()
            ], 200);
        }

        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
<?php

use App\Http\Controllers\AuthController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\MediaController;

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
    Route::get('/admin/dashboard', function (Request $request) {
        // Obtenemos los archivos multimedia para la galería
        $media = \App\Models\Media::all();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Usuario Autenticado. Bienvenido al Dashboard',
                'user' => $request->user()
            ], 200);
        }

        // Pasamos la variable $media a la vista con compact('media')
        return view('dashboard', compact('media'));
    })->name('dashboard');

    Route::get('/admin/subir-imagen', [MediaController::class, 'create'])->name('admin.media.subirImagen');
    
    // Ruta para procesar y guardar el archivo (la que usa tu formulario)
    Route::post('/admin/media', [MediaController::class, 'store'])->name('admin.media.store');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
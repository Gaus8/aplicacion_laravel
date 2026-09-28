<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordRecoveryController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SmtpSettingController;
use App\Http\Controllers\Admin\AuditLogController;

// Vista principal accesible para todos
Route::get('/', function () {
    return view('home');
})->name('home');

// Rutas para usuarios invitados (RedirectIfAuthenticated)
Route::middleware(RedirectIfAuthenticated::class)->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // Limitación a 5 intentos por minuto
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::get('/password/forgot', [PasswordRecoveryController::class, 'requestForm'])->name('password.request');
    Route::post('/password/forgot', [PasswordRecoveryController::class, 'sendCode'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/password/otp', [PasswordRecoveryController::class, 'otpForm'])->name('password.otp.form');
    Route::post('/password/otp', [PasswordRecoveryController::class, 'verifyCode'])->middleware('throttle:10,1')->name('password.otp.verify');
    Route::post('/password/otp/resend', [PasswordRecoveryController::class, 'resendCode'])->middleware('throttle:3,1')->name('password.otp.resend');
    Route::get('/password/reset', [PasswordRecoveryController::class, 'resetForm'])->name('password.reset.form');
    Route::put('/password/reset', [PasswordRecoveryController::class, 'resetPassword'])->name('password.update');
});

// Rutas protegidas
Route::middleware(Authenticate::class)->group(function () {
    Route::view('/admin/design-system', 'admin.design-system')->name('admin.design-system');

    Route::get('/admin/settings/smtp', [SmtpSettingController::class, 'edit'])->name('admin.smtp.edit');
    Route::put('/admin/settings/smtp', [SmtpSettingController::class, 'update'])->name('admin.smtp.update');
    Route::post('/admin/settings/smtp/test', [SmtpSettingController::class, 'test'])->middleware('throttle:3,1')->name('admin.smtp.test');
    Route::get('/admin/audit', [AuditLogController::class, 'index'])->name('admin.audit.index');

    Route::get('/admin/media', [MediaController::class, 'index'])->name('admin.media.index');
    Route::get('/admin/media/{media}/file', [MediaController::class, 'file'])->name('admin.media.file');
    Route::delete('/admin/media/{media}', [MediaController::class, 'destroy'])->name('admin.media.destroy');

    Route::get('/admin/dashboard', function (Request $request) {
        // Obtenemos los archivos multimedia para la galería
        $media = \App\Models\Media::all();
        // Obtenemos los correos enviados para la sección de emails
        $emails = \App\Models\EmailSent::latest()->get();   

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Usuario Autenticado. Bienvenido al Dashboard',
                'user' => $request->user()
            ], 200);
        }

        // Pasamos la variable $media a la vista con compact('media')
        return view('dashboard', compact('media', 'emails'));
    })->name('dashboard');

    Route::get('/admin/subir-imagen', [MediaController::class, 'create'])->name('admin.media.subirImagen');
    
    // Ruta para procesar y guardar el archivo (la que usa tu formulario)
    Route::post('/admin/media', [MediaController::class, 'store'])->name('admin.media.store');

    Route::get('/admin/emails', [MediaController::class, 'emails'])->name('admin.media.emails');

    // Ruta para procesar el envío del formulario
    Route::post('/admin/emails/send', [MediaController::class, 'sendEmail'])->name('admin.media.sendEmail');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

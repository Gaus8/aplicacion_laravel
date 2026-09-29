<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordRecoveryController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SmtpSettingController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicContentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\AboutPageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\ContactSubmissionController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\SeoSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\PublicSeoController;
use App\Http\Middleware\EnsureAdminPermission;

// Vista principal accesible para todos
Route::get('/', HomeController::class)->name('home');
Route::get('/nosotros', [PublicContentController::class, 'about'])->name('about.public');
Route::get('/noticias', [PublicContentController::class, 'posts'])->name('posts.public.index');
Route::get('/noticias/{post:slug}', [PublicContentController::class, 'post'])->name('posts.public.show');
Route::get('/videos', [PublicContentController::class, 'videos'])->name('videos.public.index');
Route::get('/equipo', [PublicContentController::class, 'team'])->name('team.public.index');
Route::get('/contacto', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contacto', [ContactController::class, 'store'])->middleware('throttle:4,1')->name('contact.store');
Route::get('/sitemap.xml', [PublicSeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [PublicSeoController::class, 'robots'])->name('seo.robots');

// Rutas para usuarios invitados (RedirectIfAuthenticated)
Route::middleware(RedirectIfAuthenticated::class)->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // Limitación a 5 intentos por minuto
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/registro', [AuthController::class, 'showRegistro'])->name('registro');
    Route::post('/registro', [AuthController::class, 'register'])->middleware('throttle:3,1')->name('registro.store');

    Route::get('/password/forgot', [PasswordRecoveryController::class, 'requestForm'])->name('password.request');
    Route::post('/password/forgot', [PasswordRecoveryController::class, 'sendCode'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/password/otp', [PasswordRecoveryController::class, 'otpForm'])->name('password.otp.form');
    Route::post('/password/otp', [PasswordRecoveryController::class, 'verifyCode'])->middleware('throttle:10,1')->name('password.otp.verify');
    Route::post('/password/otp/resend', [PasswordRecoveryController::class, 'resendCode'])->middleware('throttle:3,1')->name('password.otp.resend');
    Route::get('/password/reset', [PasswordRecoveryController::class, 'resetForm'])->name('password.reset.form');
    Route::put('/password/reset', [PasswordRecoveryController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
});

// Rutas protegidas
Route::middleware([Authenticate::class, EnsureAdminPermission::class])->group(function () {
    Route::get('/admin/seo', [SeoSettingController::class, 'edit'])->name('admin.seo.edit');
    Route::put('/admin/seo', [SeoSettingController::class, 'update'])->middleware('throttle:10,1')->name('admin.seo.update');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->middleware('throttle:10,1')->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->middleware('throttle:10,1')->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->middleware('throttle:10,1')->name('admin.users.destroy');

    Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/admin/roles', [RoleController::class, 'store'])->middleware('throttle:10,1')->name('admin.roles.store');
    Route::get('/admin/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/admin/roles/{role}', [RoleController::class, 'update'])->middleware('throttle:10,1')->name('admin.roles.update');
    Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy'])->middleware('throttle:10,1')->name('admin.roles.destroy');
    Route::get('/admin/about', [AboutPageController::class, 'edit'])->name('admin.about.edit');
    Route::put('/admin/about', [AboutPageController::class, 'update'])->middleware('throttle:10,1')->name('admin.about.update');

    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/admin/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/admin/categories', [CategoryController::class, 'store'])->middleware('throttle:10,1')->name('admin.categories.store');
    Route::get('/admin/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->middleware('throttle:10,1')->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->middleware('throttle:10,1')->name('admin.categories.destroy');

    Route::get('/admin/posts', [PostController::class, 'index'])->name('admin.posts.index');
    Route::get('/admin/posts/create', [PostController::class, 'create'])->name('admin.posts.create');
    Route::post('/admin/posts', [PostController::class, 'store'])->middleware('throttle:10,1')->name('admin.posts.store');
    Route::get('/admin/posts/{post}/edit', [PostController::class, 'edit'])->name('admin.posts.edit');
    Route::put('/admin/posts/{post}', [PostController::class, 'update'])->middleware('throttle:10,1')->name('admin.posts.update');
    Route::delete('/admin/posts/{post}', [PostController::class, 'destroy'])->middleware('throttle:10,1')->name('admin.posts.destroy');

    Route::get('/admin/videos', [VideoController::class, 'index'])->name('admin.videos.index');
    Route::get('/admin/videos/create', [VideoController::class, 'create'])->name('admin.videos.create');
    Route::post('/admin/videos', [VideoController::class, 'store'])->middleware('throttle:10,1')->name('admin.videos.store');
    Route::get('/admin/videos/{video}/edit', [VideoController::class, 'edit'])->name('admin.videos.edit');
    Route::put('/admin/videos/{video}', [VideoController::class, 'update'])->middleware('throttle:10,1')->name('admin.videos.update');
    Route::delete('/admin/videos/{video}', [VideoController::class, 'destroy'])->middleware('throttle:10,1')->name('admin.videos.destroy');

    Route::get('/admin/team', [TeamMemberController::class, 'index'])->name('admin.team.index');
    Route::get('/admin/team/create', [TeamMemberController::class, 'create'])->name('admin.team.create');
    Route::post('/admin/team', [TeamMemberController::class, 'store'])->middleware('throttle:10,1')->name('admin.team.store');
    Route::get('/admin/team/{team_member}/edit', [TeamMemberController::class, 'edit'])->name('admin.team.edit');
    Route::put('/admin/team/{team_member}', [TeamMemberController::class, 'update'])->middleware('throttle:10,1')->name('admin.team.update');
    Route::delete('/admin/team/{team_member}', [TeamMemberController::class, 'destroy'])->middleware('throttle:10,1')->name('admin.team.destroy');

    Route::get('/admin/testimonials', [TestimonialController::class, 'index'])->name('admin.testimonials.index');
    Route::get('/admin/testimonials/create', [TestimonialController::class, 'create'])->name('admin.testimonials.create');
    Route::post('/admin/testimonials', [TestimonialController::class, 'store'])->middleware('throttle:10,1')->name('admin.testimonials.store');
    Route::get('/admin/testimonials/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('admin.testimonials.edit');
    Route::put('/admin/testimonials/{testimonial}', [TestimonialController::class, 'update'])->middleware('throttle:10,1')->name('admin.testimonials.update');
    Route::delete('/admin/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->middleware('throttle:10,1')->name('admin.testimonials.destroy');

    Route::get('/admin/contact-messages', [ContactSubmissionController::class, 'index'])->name('admin.contact-messages.index');
    Route::patch('/admin/contact-messages/{submission}/read', [ContactSubmissionController::class, 'markRead'])->name('admin.contact-messages.read');

    Route::get('/admin/social-links', [SocialLinkController::class, 'index'])->name('admin.social-links.index');
    Route::get('/admin/social-links/create', [SocialLinkController::class, 'create'])->name('admin.social-links.create');
    Route::post('/admin/social-links', [SocialLinkController::class, 'store'])->middleware('throttle:10,1')->name('admin.social-links.store');
    Route::get('/admin/social-links/{social_link}/edit', [SocialLinkController::class, 'edit'])->name('admin.social-links.edit');
    Route::put('/admin/social-links/{social_link}', [SocialLinkController::class, 'update'])->middleware('throttle:10,1')->name('admin.social-links.update');
    Route::delete('/admin/social-links/{social_link}', [SocialLinkController::class, 'destroy'])->middleware('throttle:10,1')->name('admin.social-links.destroy');

    Route::get('/admin/services', [ServiceController::class, 'index'])->name('admin.services.index');
    Route::get('/admin/services/create', [ServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/admin/services', [ServiceController::class, 'store'])->middleware('throttle:10,1')->name('admin.services.store');
    Route::get('/admin/services/{service}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit');
    Route::put('/admin/services/{service}', [ServiceController::class, 'update'])->middleware('throttle:10,1')->name('admin.services.update');
    Route::delete('/admin/services/{service}', [ServiceController::class, 'destroy'])->middleware('throttle:10,1')->name('admin.services.destroy');

    Route::get('/admin/banners', [BannerController::class, 'index'])->name('admin.banners.index');
    Route::get('/admin/banners/create', [BannerController::class, 'create'])->name('admin.banners.create');
    Route::post('/admin/banners', [BannerController::class, 'store'])->middleware('throttle:10,1')->name('admin.banners.store');
    Route::get('/admin/banners/{banner}/edit', [BannerController::class, 'edit'])->name('admin.banners.edit');
    Route::put('/admin/banners/{banner}', [BannerController::class, 'update'])->middleware('throttle:10,1')->name('admin.banners.update');
    Route::delete('/admin/banners/{banner}', [BannerController::class, 'destroy'])->middleware('throttle:10,1')->name('admin.banners.destroy');

    Route::view('/admin/design-system', 'admin.design-system')->name('admin.design-system');

    Route::get('/admin/settings/smtp', [SmtpSettingController::class, 'edit'])->name('admin.smtp.edit');
    Route::put('/admin/settings/smtp', [SmtpSettingController::class, 'update'])->name('admin.smtp.update');
    Route::post('/admin/settings/smtp/test', [SmtpSettingController::class, 'test'])->middleware('throttle:3,1')->name('admin.smtp.test');
    Route::get('/admin/audit', [AuditLogController::class, 'index'])->name('admin.audit.index');

    Route::get('/admin/media', [MediaController::class, 'index'])->name('admin.media.index');
    Route::get('/admin/media/{media}/file', [MediaController::class, 'file'])->name('admin.media.file');
    Route::delete('/admin/media/{media}', [MediaController::class, 'destroy'])->name('admin.media.destroy');

    Route::get('/admin/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/admin/subir-imagen', [MediaController::class, 'create'])->name('admin.media.subirImagen');
    
    // Ruta para procesar y guardar el archivo (la que usa tu formulario)
    Route::post('/admin/media', [MediaController::class, 'store'])->middleware('throttle:10,1')->name('admin.media.store');

    Route::get('/admin/emails', [MediaController::class, 'emails'])->name('admin.media.emails');

    // Ruta para procesar el envío del formulario
    Route::post('/admin/emails/send', [MediaController::class, 'sendEmail'])->middleware('throttle:5,1')->name('admin.media.sendEmail');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

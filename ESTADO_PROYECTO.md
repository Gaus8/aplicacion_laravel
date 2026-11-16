# ESTADO DEL PROYECTO
## Entorno
- Laravel: 12
- PHP: 8.2
- Node.js: 20 o superior
- Base de datos local: SQLite en `database/database.sqlite` (sesiones, caché y colas también usan la base)
- Frontend: Blade + Tailwind + Vite
## Arranque local
La guía completa está en `README.md`. Hace falta Composer en el PATH; XAMPP solo aporta PHP en `C:\xampp\php\php.exe`. En PowerShell, desde la raíz del proyecto y solo la primera vez:

```powershell
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
if (-not (Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite | Out-Null }
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
composer run dev
```

Abre `http://127.0.0.1:8000`. El administrador se crea con `php artisan cms:make-admin` (no hay credenciales por defecto) y entra por `/login`.

`php artisan key:generate` solo en la primera instalación: repetirlo invalida sesiones y secretos cifrados. `php artisan migrate` aplica migraciones pendientes y no borra datos. No usar `migrate:fresh` ni `migrate:refresh`.

En una copia que ya tiene `.env` y `APP_KEY`: `composer install`, `npm install`, `php artisan migrate` y `npm run build` (o `npm run dev`).
## Módulos cerrados
- [x] Fase 0 - Base
- [x] Login seguro
- [x] Banner / Hero
- [x] Servicios
- [x] Nosotros
- [x] Categorías
- [x] Publicaciones
- [x] Multimedia
- [x] Videos
- [x] Equipo
- [x] Testimonios
- [x] Contacto
- [x] Redes sociales
- [x] SEO
- [x] Usuarios
- [x] Roles y permisos
- [x] SMTP
- [x] OTP
- [x] Auditoría
- [x] Dashboard
- [x] Hardening
- [x] Testing
- [ ] Despliegue (guía segura lista; requiere entorno de staging/producción)
## Módulo actual
Preparación de despliegue seguro; pendiente validación en staging
## Decisiones técnicas vigentes
- Roles y permisos son propios de la aplicación y controlan rutas web/API administrativas mediante middleware.
- La base local es SQLite. MySQL solo aplica si se cambia `.env` y se crea la base antes de migrar.
- La migración de roles crea el rol `administrador`. `php artisan cms:make-admin` crea o promueve una cuenta sin credenciales por defecto (contraseña de al menos 12 caracteres, con mayúsculas, minúsculas, números y símbolos).
- Si no hay un administrador activo y solo existe una cuenta activa, el registro en `/registro` o el primer inicio de sesión le asigna ese rol. Las cuentas posteriores no lo reciben.
## Rutas importantes
- `/admin/media`: galería Multimedia (módulo cerrado).
- `/admin/settings/smtp`: configuración de correo SMTP (módulo cerrado).
- `/password/forgot`, `/password/otp` y `/password/reset`: recuperación de contraseña por OTP (módulo cerrado).
- `/admin/audit`: historial de auditoría (módulo cerrado).
- `/admin/dashboard`: resumen con datos existentes (módulo cerrado).
- `/admin/banners`: administración de banners (módulo cerrado).
- `/admin/services`: administración de servicios (módulo cerrado).
- `/admin/about` y `/nosotros`: edición y página institucional (módulo cerrado).
- `/admin/categories`: CRUD de categorías (módulo cerrado).
- `/admin/posts`, `/noticias` y `/noticias/{slug}`: publicaciones y noticias (módulo cerrado).
- `/admin/videos` y `/videos`: gestión y galería de videos (módulo cerrado).
- `/admin/team` y `/equipo`: gestión y página pública del equipo (módulo cerrado).
- `/admin/testimonials`: gestión de testimonios (módulo cerrado).
- `/admin/contact-messages` y `/contacto`: bandeja y formulario de contacto (módulo cerrado).
- `/admin/social-links`: enlaces sociales compartidos en el navbar y footer (módulo cerrado).
- `/admin/seo`, `/sitemap.xml` y `/robots.txt`: administración y publicación de metadatos SEO (módulo cerrado).
- `/admin/users`: gestión de usuarios, asignación de roles y estado de acceso (módulo cerrado).
- `/admin/roles`: definición de roles y permisos efectivos sobre rutas administrativas (módulo cerrado).
## Servicios compartidos
- `App\Services\SmtpMailer`: usa la configuración activa para los envíos existentes; no guarda contraseñas en texto plano.
## Pendientes
- Validar configuración y smoke tests en un entorno real de staging antes del despliegue productivo.
## Última prueba exitosa
- Fecha: 2026-10-26
- Comando: `php artisan test`
- Resultado: 90 pruebas y 578 aserciones aprobadas; módulos funcionales 1–14 cerrados; hardening y testing verificados; compilación Vite correcta. El despliegue queda pendiente de verificación en staging.

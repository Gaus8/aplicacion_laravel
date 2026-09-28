# ESTADO DEL PROYECTO
## Entorno
- Laravel: 12
- PHP: 8.2
- Base de datos: SQLite
- Frontend: Blade + Tailwind + Vite
## Módulos cerrados
- [x] Fase 0 - Base
- [ ] Login seguro
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
- En la instalación inicial existente, el primer usuario recibe el rol Administrador; `php artisan cms:make-admin` permite crear/promover de forma interactiva sin credenciales por defecto.
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
- Fecha: 2026-09-28
- Comando: `php artisan test`
- Resultado: 81 pruebas y 498 aserciones aprobadas; módulos funcionales 1–14 cerrados; Hardening y Testing cerrados; compilación Vite correcta. El despliegue queda pendiente de verificación en staging.

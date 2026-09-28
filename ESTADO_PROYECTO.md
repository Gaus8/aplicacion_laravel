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
- [ ] Nosotros (implementación lista, pendiente de revisión)
- [ ] Categorías (implementación lista, pendiente de revisión)
- [ ] Publicaciones (implementación lista, pendiente de revisión)
- [x] Multimedia
- [ ] Videos (implementación lista, pendiente de revisión)
- [ ] Equipo (implementación lista, pendiente de revisión)
- [ ] Testimonios (implementación lista, pendiente de revisión)
- [ ] Contacto (implementación lista, pendiente de revisión)
- [ ] Redes sociales (implementación lista, pendiente de revisión)
- [ ] SEO
- [ ] Usuarios
- [ ] Roles y permisos
- [x] SMTP
- [x] OTP
- [x] Auditoría
- [x] Dashboard
- [ ] Hardening
- [ ] Testing
- [ ] Despliegue
## Módulo actual
Bloque de módulos 3–5 y 7–11 (implementación lista, pendiente de revisión)
## Decisiones técnicas vigentes
- ...
## Rutas importantes
- `/admin/media`: galería Multimedia (módulo cerrado).
- `/admin/settings/smtp`: configuración de correo SMTP (módulo cerrado).
- `/password/forgot`, `/password/otp` y `/password/reset`: recuperación de contraseña por OTP (módulo cerrado).
- `/admin/audit`: historial de auditoría (módulo cerrado).
- `/admin/dashboard`: resumen con datos existentes (módulo cerrado).
- `/admin/banners`: administración de banners (módulo cerrado).
- `/admin/services`: administración de servicios (módulo cerrado).
- `/admin/about` y `/nosotros`: edición y página institucional (pendiente de revisión).
- `/admin/categories`: CRUD de categorías (pendiente de revisión).
- `/admin/posts`, `/noticias` y `/noticias/{slug}`: publicaciones y noticias (pendiente de revisión).
- `/admin/videos` y `/videos`: gestión y galería de videos (pendiente de revisión).
- `/admin/team` y `/equipo`: gestión y página pública del equipo (pendiente de revisión).
- `/admin/testimonials`: gestión de testimonios (pendiente de revisión).
- `/admin/contact-messages` y `/contacto`: bandeja y formulario de contacto (pendiente de revisión).
- `/admin/social-links`: enlaces sociales compartidos en el navbar y footer (pendiente de revisión).
## Servicios compartidos
- `App\Services\SmtpMailer`: usa la configuración activa para los envíos existentes; no guarda contraseñas en texto plano.
## Pendientes
- Revisión funcional de los módulos 3–5 y 7–11 antes de marcarlos cerrados y activar sus enlaces en el sidebar.
## Última prueba exitosa
- Fecha: 2026-09-28
- Comando: `php artisan test`
- Resultado: 72 pruebas y 443 aserciones aprobadas; ocho migraciones nuevas aplicadas; módulos 3–5 y 7–11 listos para revisión.

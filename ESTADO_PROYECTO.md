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
- [ ] SEO (implementación lista, pendiente de revisión)
- [ ] Usuarios (implementación lista, pendiente de revisión)
- [ ] Roles y permisos (implementación lista, pendiente de revisión)
- [x] SMTP
- [x] OTP
- [x] Auditoría
- [x] Dashboard
- [ ] Hardening
- [ ] Testing
- [ ] Despliegue
## Módulo actual
Bloque módulos 12–14: SEO, Usuarios, Roles y permisos (implementación lista, pendiente de revisión)
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
- `/admin/seo`, `/sitemap.xml` y `/robots.txt`: administración y publicación de metadatos SEO (pendiente de revisión).
- `/admin/users`: gestión de usuarios, asignación de roles y estado de acceso (pendiente de revisión).
- `/admin/roles`: definición de roles y permisos efectivos sobre rutas administrativas (pendiente de revisión).
## Servicios compartidos
- `App\Services\SmtpMailer`: usa la configuración activa para los envíos existentes; no guarda contraseñas en texto plano.
## Pendientes
- Revisión funcional del bloque 12–14 antes de marcarlo cerrado y activar sus enlaces en el sidebar.
## Última prueba exitosa
- Fecha: 2026-09-28
- Comando: `php artisan test`
- Resultado: 79 pruebas y 481 aserciones aprobadas; módulos 3–5 y 7–11 cerrados; módulos 12–14 listos para revisión.

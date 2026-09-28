# ESTADO DEL PROYECTO
## Entorno
- Laravel: 12
- PHP: 8.2
- Base de datos: SQLite
- Frontend: Blade + Tailwind + Vite
## Módulos cerrados
- [ Hecho] Fase 0 - Base
- [ ] Login seguro
- [x] Banner / Hero
- [ ] Servicios (implementación lista, pendiente de revisión)
- [ ] Nosotros
- [ ] Categorías
- [ ] Publicaciones
- [x] Multimedia
- [ ] Videos
- [ ] Equipo
- [ ] Testimonios
- [ ] Contacto
- [ ] Redes sociales
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
Servicios (pendiente de revisión)
## Decisiones técnicas vigentes
- ...
## Rutas importantes
- `/admin/media`: galería Multimedia (módulo cerrado).
- `/admin/settings/smtp`: configuración de correo SMTP (módulo cerrado).
- `/password/forgot`, `/password/otp` y `/password/reset`: recuperación de contraseña por OTP (módulo cerrado).
- `/admin/audit`: historial de auditoría (módulo cerrado).
- `/admin/dashboard`: resumen con datos existentes (módulo cerrado).
- `/admin/banners`: administración de banners (módulo cerrado).
- `/admin/services`: administración de servicios (pendiente de revisión).
## Servicios compartidos
- `App\Services\SmtpMailer`: usa la configuración activa para los envíos existentes; no guarda contraseñas en texto plano.
## Pendientes
- ...
## Última prueba exitosa
- Fecha: 2026-09-28
- Comando: `php artisan test`
- Resultado: 50 pruebas y 282 aserciones aprobadas; Banners / Hero cerrado y Servicios listo para revisión.

# ESTADO DEL PROYECTO
## Entorno
- Laravel: 12
- PHP: 8.2
- Base de datos: SQLite
- Frontend: Blade + Tailwind + Vite
## Módulos cerrados
- [ Hecho] Fase 0 - Base
- [ ] Login seguro
- [ ] Banner
- [ ] Servicios
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
- [ ] SMTP
- [ ] OTP
- [ ] Auditoría
- [ ] Dashboard
- [ ] Hardening
- [ ] Testing
- [ ] Despliegue
## Módulo actual
SMTP (pendiente de revisión)
## Decisiones técnicas vigentes
- ...
## Rutas importantes
- `/admin/media`: galería Multimedia (módulo cerrado).
- `/admin/settings/smtp`: configuración de correo SMTP (pendiente de revisión).
## Servicios compartidos
- `App\Services\SmtpMailer`: usa la configuración activa para los envíos existentes; no guarda contraseñas en texto plano.
## Pendientes
- ...
## Última prueba exitosa
- Fecha: 2026-09-28
- Comando: `php artisan test`
- Resultado: 12 pruebas y 43 aserciones aprobadas; SMTP pendiente de revisión.

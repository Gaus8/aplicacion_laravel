# Despliegue seguro a producción

Esta guía prepara una publicación; no despliega automáticamente ni sustituye la validación del equipo de infraestructura.

## Antes del corte

- Confirmar revisión funcional de los módulos, pruebas verdes y aprobación del responsable.
- Preparar una instancia de staging con la misma versión de PHP, extensiones, base de datos, proxy y configuración SMTP que producción.
- Tomar respaldo verificado de base de datos y `storage/app/public`; conservar también el release anterior y sus assets.
- Gestionar `APP_KEY`, SMTP y demás secretos en el gestor de secretos del entorno, fuera del repositorio y de los logs.
- Configurar `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`, `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, `SESSION_HTTP_ONLY=true` y `SESSION_SAME_SITE=lax`.
- Configurar TLS en el proxy, renovación de certificados y trusted proxies de Laravel solo para las IP/rangos reales del proxy.
- Crear/promover el administrador inicial desde consola con `php artisan cms:make-admin`; no habilitar registro público.

## Publicar un release

Desde la carpeta del release y con una ventana de mantenimiento acordada:

```sh
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan cms:check-deployment
```

Si el proyecto no conserva `package-lock.json`, generarlo y revisarlo en desarrollo antes de usar `npm ci` en el pipeline. Conceder escritura a `storage/` y `bootstrap/cache/` solo al usuario del proceso PHP; el resto del código debe quedar de solo lectura para ese usuario.

## Smoke tests y seguimiento

- Confirmar `/up`, login, cierre de sesión, permisos por rol y bloqueo de usuarios inactivos.
- Verificar páginas públicas, formulario de contacto, correo SMTP, sitemap, `robots.txt`, imágenes y embeds permitidos.
- Confirmar HTTPS, HSTS, CSP, cookies Secure/HttpOnly/SameSite y ausencia de respuestas administrativas cacheadas.
- Revisar logs del release, colas si están habilitadas, entrega de correo y respaldos.
- Si falla una comprobación, volver al release anterior y restaurar datos solo con el procedimiento de recuperación aprobado; no ejecutar rollback automático de migraciones con datos.

## Comprobación automatizada

Ejecutar `php artisan cms:check-deployment` después de migrar, crear el enlace de Storage y optimizar configuración/rutas. No imprime valores de secretos. Los chequeos de TLS, proxy, copias, correo, permisos del sistema operativo y monitoreo siguen siendo manuales.

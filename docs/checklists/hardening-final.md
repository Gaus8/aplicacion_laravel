# Hardening final

Estado: cerrado.

- [x] CSP por nonce, sin comodines, `unsafe-inline` ni `unsafe-eval`; solo admite Vite local mientras HMR está activo y embeds aprobados.
- [x] `script-src-attr` y `style-src-attr` bloquean código y estilos inline en atributos.
- [x] Scripts inline movidos al bundle JavaScript.
- [x] Headers contra sniffing, framing, carga cross-domain y prefetch, también en respuestas de error.
- [x] CORS no permite orígenes externos; la aplicación no requiere clientes web cross-origin.
- [x] Cookie `XSRF-TOKEN` desactivada; los formularios Blade conservan protección con token de sesión `@csrf` y la cookie de sesión sigue siendo HttpOnly.
- [x] Se elimina `X-RateLimit-Reset` de las respuestas para no exponer marcas de tiempo Unix.
- [x] HSTS condicionado a HTTPS.
- [x] Sesiones de producción cifradas por defecto y cookies Secure/HttpOnly/SameSite.
- [x] Respuestas administrativas y de autenticación privadas y sin caché.
- [x] Suite de seguridad cubre headers, permisos, rate limit, cuenta inactiva y sesión.

# Documentación Postman — Aplicación CMS

## Alcance y fuente

La colección Postman v2.1 se generó a partir de `routes/api.php` y `routes/web.php` (Laravel 12). Impórtala usando **Import > File** y seleccionando `Aplicacion-CMS.postman_collection.json`. Incluye el pequeño API JSON y todas las rutas web declaradas, organizadas por grupos. La colección también incluye las 4 rutas generadas por Laravel (health, cookie CSRF y almacenamiento local firmado), con scripts de humo HTTP. La aplicación es principalmente Blade: las rutas `/admin/*`, login, registro y recuperación OTP responden con páginas HTML y redirecciones. Esta colección documenta el inventario completo; no convierte esas rutas web en una API JSON.

Variables de colección:

- `baseUrl`: URL de la aplicación, por defecto `http://127.0.0.1:8000`.
- `accessToken`: token Sanctum recibido al autenticar por JSON.
- `csrfToken`: se extrae automáticamente al ejecutar `GET /login`; el cookie jar de Postman conserva la cookie de sesión.
- `loginEmail`, `loginPassword`: credenciales configuradas como variables locales/secrets en Postman.
- `accessToken`: copia el `access_token` JSON de `POST /api/login`; úsalo para `/api/dashboard` y `/api/logout`.
- `categoryId`, `postId`, `userId`, `roleId`, `videoId`, `teamMemberId`, `testimonialId`, `socialLinkId`, `serviceId`, `bannerId`, `mediaId`, `submissionId`: IDs de registros que existan en la base conectada.
- `newUserPassword`, `newPassword`, `otpCode`, `contactEmail`, `mailTestRecipient`: completa solo si ejecutarás esos flujos.

## API JSON

Prefijo efectivo: `/api`.

| Método | Endpoint | Autenticación | Resultado esperado |
|---|---|---|---|
| POST | `/api/login` | Pública; máximo 5 intentos/minuto | JSON con `user`, `access_token`, `token_type` al aceptar credenciales (200); credenciales inválidas: 401 |
| GET | `/api/dashboard` | `Authorization: Bearer {{accessToken}}`; permiso `dashboard.view` | JSON con mensaje y usuario (200); no autenticado 401; sin permiso 403 |
| POST | `/api/logout` | Bearer requerido | Revoca el token actual y responde JSON (200) |

Para que el controlador devuelva JSON (en lugar de redirigir como un formulario), enviar `Accept: application/json`. Login acepta `email` y `password`; el token retornado se guarda manualmente en la variable `accessToken`. La colección no contiene ni exporta credenciales de cuentas. Usa una cuenta autorizada para tu entorno y guarda secretos en el gestor de variables local de Postman, nunca en variables compartidas ni en el archivo exportado.

Ejemplo de cuerpo para login (credenciales como variables locales):

```json
{
  "email": "{{loginEmail}}",
  "password": "{{loginPassword}}"
}
```

El API login no usa una cookie de sesión web: tras `Accept: application/json`, se autentica mediante token Bearer. CSRF protege las peticiones basadas en sesión/cookie; no se adjunta a peticiones Bearer stateless.

## Sesión web y CSRF

Las rutas definidas en `routes/web.php` usan middleware web de Laravel, sesión y protección CSRF para métodos que cambian estado (POST, PUT, PATCH, DELETE). No se debe desactivar CSRF para hacerlas funcionar desde Postman.

Flujo recomendado para reproducir una sesión web:

1. En Postman activa el cookie jar y usa siempre el mismo host (por ejemplo `127.0.0.1`; no mezcles `localhost` y `127.0.0.1`).
2. Haz `GET {{baseUrl}}/login`. Conserva las cookies recibidas, en especial la cookie de sesión.
3. Obtén el token CSRF de la página (`<meta name="csrf-token" content="…">` o el campo oculto `_token`). En formularios HTML usa el contenido del campo `_token`; también puedes enviar `X-CSRF-TOKEN: {{csrfToken}}`. Si el servidor emite la cookie `XSRF-TOKEN`, Postman puede enviar su valor como `X-XSRF-TOKEN` tras decodificarlo según el comportamiento del cliente.
4. Envía el formulario con `application/x-www-form-urlencoded` o `multipart/form-data` según la vista. Incluye `_token` en el cuerpo o la cabecera CSRF junto con las cookies de esa misma sesión.
5. Inicia sesión por `POST /login` con `email`, `password` y opcionalmente `remember`; sigue las redirecciones y conserva la cookie nueva tras regeneración de sesión. Acceso administrativo requiere usuario activo y permiso correspondiente.
6. Para cada operación mutante reutiliza cookie y CSRF vigentes. Obtén un token nuevo si la sesión se regenera o vence. Los GET no requieren CSRF.
7. Cierra la sesión mediante `POST /logout` con cookie y CSRF; esto invalida sesión y token CSRF.

Las vistas y controladores web están diseñados para navegación HTML: es normal recibir `302` hacia otra página (éxito) o volver con errores de validación. Configurar `Accept: application/json` puede convertir errores de validación/autenticación en respuestas JSON, pero no transforma todas las acciones web en endpoints JSON.


## Cuerpos de ejemplo incluidos

Las solicitudes mutantes web usan `application/x-www-form-urlencoded`; cargas con archivo usan `multipart/form-data`. Ambas llevan `X-CSRF-TOKEN: {{csrfToken}}` y conservan cookie de sesión. Los campos de ejemplo vienen de los Form Requests y validaciones actuales. Sustituye IDs, valores únicos y variables según los datos existentes.

- **Autenticación y OTP:** login (`email`, `password`); registro (`name`, `email`, `password`, `password_confirmation`); solicitud OTP (`email`); verificación (`code`, seis dígitos); restablecimiento (`password`, `password_confirmation`). OTP depende de código real enviado por SMTP y de la misma sesión que inició la recuperación.
- **Contacto público:** `name`, `email`, `subject`, `message` y honeypot `website`. Una solicitud válida guarda un mensaje y puede enviar notificación.
- **Acerca de:** `title`, `body`, `subtitle`, `mission`, `vision`, `active`; `image` es opcional y, si se carga, exige `image_alt`.
- **SEO:** `site_name`, `title_template` con `%s`, `default_title`, `default_description`, `robots_directive`; URL canónica y `og_image` son opcionales.
- **Banners:** título, texto alternativo de imagen, posición y estado; en creación la imagen es requerida. `cta_label` y `cta_url` se envían juntos o ambos vacíos.
- **Categorías:** `name`, `description`, `active`.
- **Publicaciones:** `title`, `excerpt`, `body`, `category_id`, `status`; portada/alt y `published_at` son opcionales. Estados válidos: `draft`, `review`, `published`, `archived`.
- **Servicios:** `title`, `summary`, `description`, `position`, `active`; CTA opcional en pareja etiqueta/URL.
- **Videos:** `title`, `video_url` de YouTube/Vimeo, `position`, `active`; descripción opcional.
- **Equipo:** `name`, `role`, `bio`, `position`, `active`; email, URL https e imagen/alt opcionales.
- **Testimonios:** `person_name`, `quote`, `position`, `active`; cargo y organización opcionales.
- **Redes sociales:** `platform`, `label`, URL oficial https, `position`, `active`.
- **Usuarios:** `name`, email único, contraseña de al menos 12 caracteres y confirmación, `is_active`; roles opcionales.
- **Roles:** `name`, descripción y permisos opcionales (nombres de permisos existentes).
- **SMTP:** host, puerto, cifrado, flags, remitente y nombre; solo completa credenciales en local. La contraseña se cifra al guardarse.
- **Multimedia:** nombre y archivo requerido; solo imágenes reales permitidas (hasta 5 MB). SEO, banners, publicaciones y equipo también incluyen marcadores de archivo para seleccionar localmente.
- **Correo saliente:** destinatario, asunto, mensaje y adjuntos opcionales. Enviar genera correo real y registro persistente.

`GET /login` incluye un script Postman que captura el `_token` del HTML en `csrfToken`. Ejecuta esa petición primero. El GET de archivo local y el PUT de carga usan URLs firmadas generadas por el servidor (`expires`/`signature`); el PUT recibe contenido binario sin multipart y no usa CSRF porque lo autoriza la firma. El cookie jar debe estar habilitado y mantenerse en el mismo host/baseUrl. Cambia `baseUrl` si tu servidor escucha en otro host o puerto.

## Pruebas realizadas contra el servidor activo

Se verificó la portada y `/up` (200), `GET /sanctum/csrf-cookie` (204), y se ejecutaron 59 solicitudes GET de humo y 48 solicitudes mutantes sin CSRF/token. No hubo respuestas 5xx. Las mutaciones web quedaron rechazadas con 419; logout API sin token devolvió 401 y login API sin campos devolvió 422. El GET de almacenamiento privado sin firma y el PUT sin firma devolvieron 403. El login web real con CSRF redirigió al dashboard (200). Estas comprobaciones no ejecutaron altas/ediciones/borrados válidos, envío de correo/SMTP ni carga de archivos: para validar esos efectos usa una base y SMTP de prueba.

Cada solicitud tiene una aserción Postman para detectar respuestas HTTP 5xx; esta aserción es un smoke check, no sustituye las respuestas esperadas documentadas ni una prueba de negocio autenticada.

## Rutas incluidas

La colección incluye las 104 rutas de aplicación y las 4 rutas de sistema mostradas por `php artisan route:list`, más login/registro y recuperación OTP, y todas las rutas `/admin/*` (dashboard, SEO, usuarios, roles, contenido, multimedia, correo, SMTP, auditoría y contactos). Los endpoints web quedan agrupados como `Web - Público`, `Web - Autenticación`, `Web - Recuperación OTP` y `Web - Administración`.

Las rutas administrativas requieren sesión autenticada y autorización por permiso vía `EnsureAdminPermission`. Una sesión válida sin permiso recibe 403; rutas protegidas sin sesión redirigen al login. En la autenticación web fallida se devuelve un mensaje genérico. Registro está limitado a 3 solicitudes/minuto; login a 5/minuto. OTP: solicitud y reenvío 3/minuto, verificación 10/minuto y cambio 5/minuto. Las acciones administrativas mutantes principales limitan a 10/minuto; prueba SMTP 3/minuto y envío de correo 5/minuto.

## Formularios y archivos

La colección ya trae los campos de formulario según los Form Requests y controladores. Las operaciones web usan formulario urlencoded o multipart, no un contrato JSON uniforme. Para ampliar reglas consulta el Form Request y la vista del módulo en `app/Http/Requests/Admin` y `resources/views/admin`. Formularios con archivos deben ser `multipart/form-data`, cargar archivos válidos por el formulario y enviar el token CSRF. No conviertas campos de archivo a base64 salvo que el endpoint lo documente expresamente.

Los segmentos dinámicos usan variables Postman como `{{userId}}`, `{{categoryId}}` y `{{postSlug}}`. Sustitúyelas por IDs/slug existentes antes de ejecutar. Las peticiones `DELETE` y las acciones mutantes pueden cambiar datos reales; usa una base local de pruebas.

## Verificación del inventario

Compara la colección con:

- `routes/api.php`
- `routes/web.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/PasswordRecoveryController.php`
- `app/Http/Middleware/EnsureAdminPermission.php`

El estado actual marca el módulo «Login seguro» pendiente en `ESTADO_PROYECTO.md`; por ello, esta guía describe el comportamiento observado sin declarar ese módulo cerrado. No se documentan rutas inexistentes ni se supone soporte de JSON para acciones Blade.




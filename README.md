# CMS institucional

Sitio público y panel de administración. Laravel 12, PHP 8.2, Blade, Tailwind CSS y Vite. En local la base de datos es SQLite (`database/database.sqlite`): sesiones, caché y colas también usan esa base, así que la aplicación no arranca hasta migrar.

El estado de los módulos está en [ESTADO_PROYECTO.md](ESTADO_PROYECTO.md). El despliegue está en [docs/deployment/production.md](docs/deployment/production.md).

## Requisitos

- PHP 8.2 o superior, con `bcmath`, `ctype`, `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_sqlite`, `tokenizer`, `xml` y `zip`. Composer descomprime los paquetes con `zip`.
- Composer 2.
- Node.js 20 o superior y npm.

En XAMPP, PHP está en `C:\xampp\php\php.exe` (en este equipo, 8.3, compatible con el requisito 8.2). XAMPP no incluye Composer. Si al abrir PowerShell `php` o `composer` no se reconocen:

1. Instala Composer desde [getcomposer.org/download](https://getcomposer.org/download/) y, cuando lo pida, indica `C:\xampp\php\php.exe`.
2. Cierra y vuelve a abrir la terminal para que tome el PATH.
3. Mientras tanto puedes llamar a PHP con la ruta completa, por ejemplo `C:\xampp\php\php.exe artisan migrate`.

En `C:\xampp\php\php.ini` deben estar activas `extension=pdo_sqlite`, `extension=sqlite3` y `extension=zip`. Sin `zip`, `composer install` se detiene al descargar paquetes. En el PHP de este XAMPP, `zip` ya quedó activada.

## Primera instalación

Desde la raíz del proyecto, en PowerShell:

```powershell
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
if (-not (Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite | Out-Null }
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
```

Qué hace cada paso:

1. `composer install` instala las dependencias de PHP.
2. La copia de `.env.example` crea la configuración local. No vuelve a copiarse si `.env` ya existe.
3. `php artisan key:generate` escribe `APP_KEY`. Ejecútalo solo la primera vez: repetirlo invalida sesiones y secretos cifrados, como la contraseña SMTP.
4. El archivo `database\database.sqlite` es la base. Sin ese archivo, `migrate` falla.
5. `php artisan migrate` crea las tablas y el rol Administrador. No borra datos existentes.
6. `php artisan db:seed` carga contenido público de ejemplo (banners, servicios, noticias, equipo, testimonios y redes). Volver a ejecutarlo actualiza esos registros de ejemplo.
7. `php artisan storage:link` publica `storage/app/public` en `public/storage`, necesario para ver las imágenes.
8. `npm install` instala Vite y Tailwind.

Atajo para un clon nuevo: `composer run setup`. Instala dependencias, crea `.env` y `database/database.sqlite` si no existen, genera `APP_KEY`, migra, carga el contenido de ejemplo y compila los assets. No crea el enlace de storage ni el administrador, y regenera `APP_KEY`: no lo uses si `.env` ya tiene una clave. Después ejecuta `php artisan storage:link`, `php artisan cms:make-admin` y el servidor.

No uses `php artisan migrate:fresh` ni `migrate:refresh`: vacían la base.

## Arrancar en desarrollo

```powershell
composer run dev
```

Abre [http://127.0.0.1:8000](http://127.0.0.1:8000). Ese comando levanta el servidor PHP, la cola y Vite. En Windows no incluye el visor de logs Pail: exige la extensión `pcntl`, que no existe en PHP para Windows, y al fallar detenía también el servidor y Vite.

Si prefieres dos terminales:

```powershell
php artisan serve
npm run dev
```

Sin `npm run dev` en marcha, o sin un `npm run build` previo, los estilos no cargan. Conviene que `APP_URL` en `.env` sea `http://127.0.0.1:8000` cuando uses `php artisan serve`.

## Apache de XAMPP

La base local sigue siendo SQLite. Apache no sustituye a `migrate`.

- URL: [http://localhost/aplicacion_laravel/public](http://localhost/aplicacion_laravel/public)
- En `.env`, `APP_URL=http://localhost/aplicacion_laravel/public`
- Ejecuta `npm run build`, o deja `npm run dev` corriendo, para que carguen los estilos.

## Administrador

No hay usuario ni contraseña por defecto. Después de migrar:

```powershell
php artisan cms:make-admin
```

Pide nombre, correo y contraseña. La contraseña debe tener al menos 12 caracteres, con mayúsculas, minúsculas, números y símbolos.

También puedes crear la primera cuenta en `/registro`. Si todavía no hay un administrador activo y esa es la única cuenta activa, recibe el rol Administrador al registrarse o al iniciar sesión. Las cuentas siguientes no lo reciben.

Entra por `/login`. El panel queda en `/admin/dashboard`.

## Instalación que ya existe

```powershell
composer install
npm install
php artisan migrate
npm run build
```

Omite `key:generate` si `.env` ya tiene `APP_KEY`. `migrate` solo aplica migraciones pendientes.

## MySQL (opcional)

La configuración vigente es SQLite. Para usar el MySQL de XAMPP, crea antes la base y ajusta `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aplicacion_laravel
DB_USERNAME=root
DB_PASSWORD=
```

Después ejecuta `php artisan migrate` y, si quieres el contenido de ejemplo, `php artisan db:seed`.

## Pruebas

```powershell
php artisan test
```

Las pruebas usan SQLite en memoria y no modifican `database/database.sqlite`.

## Si algo falla

- `Database file at path ... not found`: crea `database\database.sqlite` y vuelve a migrar.
- `could not find driver`: activa `pdo_sqlite` en el `php.ini` que usa la terminal (`php --ini`).
- El enlace de storage falla en Windows: activa el Modo de desarrollador o ejecuta la terminal como administrador y repite `php artisan storage:link`.
- La página responde 500 con `Vite manifest not found`, o se ve sin estilos: no hay assets compilados ni Vite en marcha. Ejecuta `npm run dev` o `npm run build`.
- `The [pcntl] extension is required to run Pail`: en Windows no se puede activar esa extensión. Usa `php artisan serve` junto con `npm run dev`, o `composer run dev` después de esta corrección. No hace falta Pail para usar la aplicación.

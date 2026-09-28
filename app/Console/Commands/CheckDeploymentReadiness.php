<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CheckDeploymentReadiness extends Command
{
    protected $signature = 'cms:check-deployment';
    protected $description = 'Verifica condiciones de seguridad antes de publicar en producción';

    public function handle(): int
    {
        $migrationCheck = false;
        try {
            if (Schema::hasTable('migrations')) {
                $ran = DB::table('migrations')->pluck('migration')->all();
                $available = collect(File::files(database_path('migrations')))->map(fn ($file) => $file->getFilenameWithoutExtension())->all();
                $migrationCheck = count(array_diff($available, $ran)) === 0;
            }
        } catch (Throwable) {
            $migrationCheck = false;
        }

        $checks = [
            'APP_ENV es production' => app()->environment('production'),
            'APP_DEBUG está desactivado' => config('app.debug') === false,
            'APP_URL usa HTTPS' => str_starts_with((string) config('app.url'), 'https://'),
            'APP_KEY está configurada' => filled(config('app.key')),
            'SESSION_SECURE_COOKIE está activo' => (bool) config('session.secure'),
            'SESSION_ENCRYPT está activo' => (bool) config('session.encrypt'),
            'Cookie de sesión HttpOnly' => (bool) config('session.http_only'),
            'SameSite es Lax o Strict' => in_array(config('session.same_site'), ['lax', 'strict'], true),
            'Migraciones aplicadas' => $migrationCheck,
            'Enlace simbólico público de Storage presente' => is_link(public_path('storage')),
            'storage escribible' => is_writable(storage_path()),
            'bootstrap/cache escribible' => is_writable(base_path('bootstrap/cache')),
            'Configuración cacheada' => app()->configurationIsCached(),
            'Rutas cacheadas' => app()->routesAreCached(),
        ];

        $failed = 0;
        $this->newLine();
        $this->components->info('Comprobaciones para despliegue seguro');
        foreach ($checks as $label => $passed) {
            $this->components->twoColumnDetail($label, $passed ? '<fg=green>OK</>' : '<fg=yellow>REVISAR</>');
            if (!$passed) $failed++;
        }

        $this->newLine();
        if ($failed > 0) {
            $this->warn("No listo: {$failed} comprobación(es) requieren configuración en el entorno de despliegue.");
            return self::FAILURE;
        }

        $this->info('Las comprobaciones automáticas están listas. Completa además las validaciones manuales de la guía de despliegue.');
        return self::SUCCESS;
    }
}

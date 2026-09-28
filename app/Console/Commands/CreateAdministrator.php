<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CreateAdministrator extends Command
{
    protected $signature = 'cms:make-admin';
    protected $description = 'Crea o promueve una cuenta con acceso administrador inicial';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Nombre del administrador'));
        $email = mb_strtolower(trim((string) $this->ask('Correo electrónico')));
        $password = (string) $this->secret('Contraseña (mínimo 12 caracteres)');
        $confirmation = (string) $this->secret('Confirma la contraseña');

        $validator = validator(['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirmation], [
            'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) $this->error($error);
            return self::FAILURE;
        }

        $role = Role::query()->where('slug', 'administrador')->first();
        if (!$role) {
            $this->error('No existe el rol administrador. Ejecuta primero las migraciones.');
            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->password = Hash::make($password);
        $user->is_active = true;
        $user->save();
        $user->roles()->syncWithoutDetaching([$role->id]);

        $this->info('La cuenta administradora quedó lista.');
        return self::SUCCESS;
    }
}

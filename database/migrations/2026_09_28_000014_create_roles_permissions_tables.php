<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('slug', 100)->unique();
            $table->string('description', 255)->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120)->unique();
            $table->string('label', 120);
            $table->string('group', 80);
            $table->timestamps();
        });

        Schema::create('role_permission', function (Blueprint $table): void {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('role_user', function (Blueprint $table): void {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });

        $catalog = [
            'dashboard.view' => ['Ver dashboard', 'Administración'],
            'about.manage' => ['Gestionar información institucional', 'Contenido'],
            'categories.manage' => ['Gestionar categorías', 'Contenido'],
            'posts.manage' => ['Gestionar publicaciones', 'Contenido'],
            'videos.manage' => ['Gestionar videos', 'Contenido'],
            'team.manage' => ['Gestionar equipo', 'Contenido'],
            'testimonials.manage' => ['Gestionar testimonios', 'Contenido'],
            'contact.manage' => ['Gestionar mensajes de contacto', 'Contenido'],
            'social.manage' => ['Gestionar redes sociales', 'Contenido'],
            'banners.manage' => ['Gestionar banners', 'Contenido'],
            'services.manage' => ['Gestionar servicios', 'Contenido'],
            'media.manage' => ['Gestionar multimedia', 'Contenido'],
            'smtp.manage' => ['Gestionar correo SMTP', 'Configuración'],
            'audit.view' => ['Consultar auditoría', 'Administración'],
            'seo.manage' => ['Gestionar configuración SEO', 'Configuración'],
            'users.manage' => ['Gestionar usuarios', 'Seguridad'],
            'roles.manage' => ['Gestionar roles y permisos', 'Seguridad'],
            'email.manage' => ['Enviar correos administrativos', 'Configuración'],
        ];

        foreach ($catalog as $name => [$label, $group]) {
            DB::table('permissions')->insert(['name' => $name, 'label' => $label, 'group' => $group, 'created_at' => now(), 'updated_at' => now()]);
        }

        DB::table('roles')->insert(['name' => 'Administrador', 'slug' => 'administrador', 'description' => 'Acceso completo a la administración.', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()]);
        $adminRoleId = (int) DB::table('roles')->where('slug', 'administrador')->value('id');
        $permissionIds = DB::table('permissions')->pluck('id');
        foreach ($permissionIds as $permissionId) {
            DB::table('role_permission')->insert(['role_id' => $adminRoleId, 'permission_id' => $permissionId]);
        }

        $bootstrapUserId = DB::table('users')->orderBy('id')->value('id');
        if ($bootstrapUserId !== null) {
            DB::table('role_user')->insert(['role_id' => $adminRoleId, 'user_id' => $bootstrapUserId]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};

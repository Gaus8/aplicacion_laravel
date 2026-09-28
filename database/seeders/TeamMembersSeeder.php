<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TeamMembersSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['team-valentina.svg', 'team-andres.svg', 'team-camila.svg'] as $image) {
            $source = __DIR__.'/assets/'.$image;
            $contents = is_file($source) ? file_get_contents($source) : false;

            if ($contents === false || ! Storage::disk('public')->put('seed/'.$image, $contents)) {
                throw new \RuntimeException("No fue posible instalar el avatar de ejemplo {$image}.");
            }
        }

        $members = [
            ['name' => 'Elmer Ferney Gordillo', 'role' => 'Ingeniero Full Stack', 'bio' => 'Construye experiencias web de principio a fin, conectando interfaces cuidadas con servicios confiables y soluciones prácticas para cada cliente.', 'email' => 'elmer@usftechsolutions.example', 'legacy_email' => 'valentina@usftechsolutions.com', 'image' => 'team-valentina.svg'],
            ['name' => 'Uriel Stiven Garzon', 'role' => 'Arquitecto Cloud', 'bio' => 'Diseña plataformas escalables y automatiza infraestructura para que los productos digitales crezcan de forma segura y eficiente.', 'email' => 'uriel@usftechsolutions.example', 'legacy_email' => 'andres@usftechsolutions.com', 'image' => 'team-andres.svg'],
            ['name' => 'David Santiago Torres', 'role' => 'Desarrollador de Software', 'bio' => 'Convierte ideas en herramientas digitales útiles, con atención al detalle, código mantenible y una fuerte colaboración con el equipo.', 'email' => 'david@usftechsolutions.example', 'legacy_email' => 'camila@usftechsolutions.com', 'image' => 'team-camila.svg'],
        ];

        foreach ($members as $position => $member) {
            $image = $member['image'];
            $legacyEmail = $member['legacy_email'];
            unset($member['image'], $member['legacy_email']);

            $teamMember = TeamMember::query()->where('email', $member['email'])->first()
                ?? TeamMember::query()->where('email', $legacyEmail)->first()
                ?? new TeamMember;

            $teamMember->fill($member + [
                'profile_url' => null,
                'image_path' => 'seed/'.$image,
                'image_alt' => 'Avatar ilustrado de programación de '.$member['name'],
                'position' => $position,
                'active' => true,
            ]);
            $teamMember->save();
        }
    }
}

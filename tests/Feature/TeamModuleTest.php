<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeamModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_team_admin(): void
    {
        $this->get(route('admin.team.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_create_member_with_secure_image_and_public_fields(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('admin.team.store'), [
            'name' => 'Ana Pérez', 'role' => 'Directora', 'bio' => 'Biografía pública del equipo.',
            'email' => 'ana@example.test', 'profile_url' => 'https://example.test/perfil',
            'image' => UploadedFile::fake()->createWithContent('member.png', $this->pngFixture()), 'image_alt' => 'Ana Pérez',
            'position' => 0, 'active' => '1',
        ])->assertRedirect(route('admin.team.index'));

        $member = TeamMember::query()->firstOrFail();
        $this->assertSame($user->id, $member->creator->id);
        Storage::disk('public')->assertExists($member->image_path);
        $this->get(route('team.public.index'))->assertOk()->assertSeeText('Ana Pérez')->assertSeeText('Directora')->assertSee('mailto:ana@example.test', false);
    }

    public function test_invalid_member_image_is_rejected_and_inactive_members_are_hidden(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create())->from(route('admin.team.create'))
            ->post(route('admin.team.store'), [
                'name' => 'Persona', 'role' => 'Cargo', 'bio' => 'Biografía pública.',
                'image' => UploadedFile::fake()->create('bad.png', 10, 'text/plain'),
            ])->assertRedirect(route('admin.team.create'))->assertSessionHasErrors('image');

        TeamMember::create(['name' => 'Persona inactiva', 'role' => 'Cargo', 'bio' => 'No publicar.', 'active' => false]);
        $this->get(route('team.public.index'))->assertDontSeeText('Persona inactiva');
        $this->assertDatabaseCount('team_members', 1);
    }

    private function pngFixture(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC');
    }
}

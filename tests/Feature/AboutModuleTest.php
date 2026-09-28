<?php

namespace Tests\Feature;

use App\Models\AboutPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AboutModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_edit_institutional_content(): void
    {
        $this->get(route('admin.about.edit'))->assertRedirect(route('login'));
    }

    public function test_authenticated_admin_can_edit_and_publish_about_page(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.about.edit'))->assertOk();

        $this->put(route('admin.about.update'), [
            'title' => 'Nuestra organización', 'subtitle' => 'Subtítulo institucional',
            'body' => 'Presentación de la organización.', 'mission' => 'Misión institucional',
            'vision' => 'Visión institucional', 'image' => UploadedFile::fake()->createWithContent('about.png', $this->pngFixture()),
            'image_alt' => 'Equipo reunido', 'active' => '1',
        ])->assertRedirect(route('admin.about.edit'));

        $page = AboutPage::query()->firstOrFail();
        $this->assertSame($user->id, $page->creator->id);
        $this->assertSame($user->id, $page->updater->id);
        Storage::disk('public')->assertExists($page->image_path);
        $this->get(route('about.public'))->assertOk()->assertSeeText('Nuestra organización')->assertSeeText('Misión institucional');
    }

    public function test_inactive_page_is_not_public_and_invalid_images_are_rejected(): void
    {
        AboutPage::create(['title' => 'Institución', 'body' => 'Contenido disponible', 'active' => false]);
        $this->get(route('about.public'))->assertNotFound();

        $this->actingAs(User::factory()->create())
            ->from(route('admin.about.edit'))
            ->put(route('admin.about.update'), [
                'title' => 'Institución', 'body' => 'Texto válido',
                'image' => UploadedFile::fake()->create('fake.png', 10, 'text/plain'),
            ])->assertRedirect(route('admin.about.edit'))->assertSessionHasErrors('image');
    }

    private function pngFixture(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC');
    }
}

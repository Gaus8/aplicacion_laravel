<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_the_gallery(): void
    {
        $this->get(route('admin.media.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_search_media(): void
    {
        $this->actingAs(User::factory()->create());

        Media::create(['name' => 'Portada CMS', 'path' => 'media/cover.png', 'mime_type' => 'image/png', 'size' => 100]);
        Media::create(['name' => 'Banner de inicio', 'path' => 'media/banner.png', 'mime_type' => 'image/png', 'size' => 100]);

        $this->get(route('admin.media.index', ['search' => 'portada']))
            ->assertOk()
            ->assertSeeText('Portada CMS')
            ->assertDontSeeText('Banner de inicio');
    }

    public function test_authenticated_user_can_upload_a_valid_image(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $response = $this->from(route('admin.media.subirImagen'))->post(route('admin.media.store'), [
            'name' => 'Portada CMS',
            'file' => UploadedFile::fake()->createWithContent('portada.png', $this->pngFixture()),
        ]);

        $response->assertRedirect(route('admin.media.index'));
        $this->assertDatabaseHas('media', ['name' => 'Portada CMS', 'mime_type' => 'image/png']);
        $media = Media::query()->firstOrFail();
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $this->from(route('admin.media.subirImagen'))
            ->post(route('admin.media.store'), [
                'name' => 'Archivo falso',
                'file' => UploadedFile::fake()->create('archivo.png', 10, 'text/plain'),
            ])
            ->assertRedirect(route('admin.media.subirImagen'))
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('media', 0);
    }

    public function test_user_can_view_and_delete_an_uploaded_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $path = 'media/retrato.png';
        Storage::disk('public')->put($path, $this->pngFixture());
        $media = Media::create([
            'name' => 'Retrato',
            'path' => $path,
            'mime_type' => 'image/png',
            'size' => 100,
        ]);

        $this->actingAs($user)
            ->get(route('admin.media.file', $media))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->delete(route('admin.media.destroy', $media))
            ->assertRedirect(route('admin.media.index'));

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($path);
    }

    private function pngFixture(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC');
    }
}

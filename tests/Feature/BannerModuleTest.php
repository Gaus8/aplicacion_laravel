<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BannerModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_banner_administration(): void
    {
        $this->get(route('admin.banners.index'))->assertRedirect(route('login'));
        $this->get(route('admin.banners.create'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_the_list_and_form(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $banner = Banner::create($this->modelAttributes('banners/view.png'));

        $this->get(route('admin.banners.index'))->assertOk()->assertSeeText('Banners / Hero');
        $this->get(route('admin.banners.create'))->assertOk()->assertSeeText('Crear banner');
        $this->get(route('admin.banners.edit', $banner))->assertOk()->assertSeeText('Editar banner');
    }

    public function test_closed_banner_module_is_active_in_the_sidebar(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.banners.index'))
            ->assertOk()
            ->assertSee('href="'.route('admin.banners.index').'"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_authorized_user_can_create_a_banner_and_store_its_relationships(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.banners.store'), $this->validPayload())
            ->assertRedirect(route('admin.banners.index'));

        $banner = Banner::query()->firstOrFail();
        $this->assertSame($user->id, $banner->creator->id);
        $this->assertSame($user->id, $banner->updater->id);
        $this->assertTrue($banner->active);
        Storage::disk('public')->assertExists($banner->image_path);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create())
            ->from(route('admin.banners.create'))
            ->post(route('admin.banners.store'), $this->validPayload([
                'image' => UploadedFile::fake()->create('fake.png', 10, 'text/plain'),
            ]))
            ->assertRedirect(route('admin.banners.create'))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('banners', 0);
        $this->assertSame([], Storage::disk('public')->allFiles('banners'));
    }

    public function test_oversized_image_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create())
            ->from(route('admin.banners.create'))
            ->post(route('admin.banners.store'), $this->validPayload([
                'image' => UploadedFile::fake()->createWithContent('large.png', $this->pngFixture().str_repeat("\0", 5 * 1024 * 1024)),
            ]))
            ->assertRedirect(route('admin.banners.create'))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('banners', 0);
    }

    public function test_call_to_action_fields_must_be_provided_together(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create())
            ->from(route('admin.banners.create'))
            ->post(route('admin.banners.store'), $this->validPayload(['cta_label' => 'Ver más', 'cta_url' => null]))
            ->assertSessionHasErrors(['cta_label', 'cta_url']);

        $this->assertDatabaseCount('banners', 0);
    }

    public function test_call_to_action_only_accepts_http_or_https_destinations(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create())
            ->from(route('admin.banners.create'))
            ->post(route('admin.banners.store'), $this->validPayload([
                'cta_url' => 'javascript:alert(1)',
            ]))
            ->assertRedirect(route('admin.banners.create'))
            ->assertSessionHasErrors('cta_url');

        $this->assertDatabaseCount('banners', 0);
    }

    public function test_user_can_update_a_banner_and_replacement_removes_old_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $oldPath = 'banners/old.png';
        Storage::disk('public')->put($oldPath, $this->pngFixture());
        $banner = Banner::create($this->modelAttributes($oldPath, ['created_by' => $user->id]));

        $this->actingAs($user)->put(route('admin.banners.update', $banner), $this->validPayload([
            'title' => 'Banner actualizado',
            'active' => '0',
        ]))->assertRedirect(route('admin.banners.index'));

        $banner->refresh();
        $this->assertSame('Banner actualizado', $banner->title);
        $this->assertFalse($banner->active);
        $this->assertSame($user->id, $banner->updater->id);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($banner->image_path);
    }

    public function test_user_can_delete_banner_and_its_stored_image(): void
    {
        Storage::fake('public');
        $path = 'banners/remove.png';
        Storage::disk('public')->put($path, $this->pngFixture());
        $banner = Banner::create($this->modelAttributes($path));

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.banners.destroy', $banner))
            ->assertRedirect(route('admin.banners.index'));

        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_home_renders_single_banner_as_static_and_multiple_as_carousel(): void
    {
        Storage::fake('public');
        Banner::create($this->modelAttributes('banners/one.png'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Título de prueba')
            ->assertDontSee('data-hero-next', false);

        Banner::create($this->modelAttributes('banners/two.png', ['title' => 'Segundo banner', 'position' => 1]));

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Título de prueba')
            ->assertSeeText('Segundo banner')
            ->assertSee('data-hero-next', false)
            ->assertSee('data-hero-dot="1"', false);
    }

    public function test_only_active_banners_with_valid_schedule_appear_publicly(): void
    {
        Storage::fake('public');
        $this->travelTo(now()->setDate(2026, 9, 28)->setTime(12, 0));
        Banner::create($this->modelAttributes('banners/current.png'));
        Banner::create($this->modelAttributes('banners/inactive.png', ['title' => 'Banner inactivo', 'active' => false]));
        Banner::create($this->modelAttributes('banners/future.png', ['title' => 'Banner futuro', 'starts_at' => now()->addDay()]));
        Banner::create($this->modelAttributes('banners/expired.png', ['title' => 'Banner vencido', 'ends_at' => now()->subDay()]));

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Título de prueba')
            ->assertDontSeeText('Banner inactivo')
            ->assertDontSeeText('Banner futuro')
            ->assertDontSeeText('Banner vencido');
    }

    private function validPayload(array $overrides = []): array
    {
        return array_replace([
            'title' => 'Título de prueba',
            'subtitle' => 'Texto de presentación',
            'image_alt' => 'Imagen descriptiva',
            'image' => UploadedFile::fake()->createWithContent('hero.png', $this->pngFixture()),
            'cta_label' => 'Conocer más',
            'cta_url' => 'https://example.test/contenido',
            'position' => 0,
            'active' => '1',
        ], $overrides);
    }

    private function modelAttributes(string $path, array $overrides = []): array
    {
        return array_replace([
            'title' => 'Título de prueba',
            'subtitle' => 'Texto de presentación',
            'image_path' => $path,
            'image_alt' => 'Imagen descriptiva',
            'cta_label' => null,
            'cta_url' => null,
            'position' => 0,
            'active' => true,
            'starts_at' => null,
            'ends_at' => null,
        ], $overrides);
    }

    private function pngFixture(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC');
    }
}

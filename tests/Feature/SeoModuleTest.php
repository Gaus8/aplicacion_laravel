<?php

namespace Tests\Feature;

use App\Models\SeoSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_seo_settings_render_public_metadata_and_safe_robots_and_sitemap(): void
    {
        SeoSetting::create(['site_name' => 'Portal de prueba', 'title_template' => '%s | Portal', 'default_title' => 'Portada', 'default_description' => 'Descripción pública', 'canonical_base_url' => 'https://portal.example', 'robots_directive' => 'index,follow']);
        $this->get(route('home'))->assertOk()->assertSee('<title>Inicio · CMS Core | Portal</title>', false)->assertSee('content="Descripción pública"', false)->assertSee('https://portal.example/', false);
        $this->get(route('seo.robots'))->assertOk()->assertSeeText('Disallow: /admin/')->assertSeeText(route('seo.sitemap'));
        $this->get(route('seo.sitemap'))->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->assertSee(route('home'), false);
    }

    public function test_admin_can_save_seo_settings_but_invalid_image_and_url_are_rejected(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $data = ['site_name' => 'Portal', 'title_template' => '%s | Portal', 'default_title' => 'Inicio', 'default_description' => 'Descripción SEO', 'canonical_base_url' => 'https://portal.example', 'robots_directive' => 'index,follow'];
        $this->actingAs($user)->put(route('admin.seo.update'), $data)->assertRedirect();
        $this->assertDatabaseHas('seo_settings', ['site_name' => 'Portal', 'robots_directive' => 'index,follow']);
        $this->from(route('admin.seo.edit'))->put(route('admin.seo.update'), array_replace($data, ['canonical_base_url' => 'http://unsafe.example']))->assertSessionHasErrors('canonical_base_url');
        $this->from(route('admin.seo.edit'))->put(route('admin.seo.update'), $data + ['og_image' => UploadedFile::fake()->create('invalid.png', 50, 'text/plain')])->assertSessionHasErrors('og_image');
    }
}

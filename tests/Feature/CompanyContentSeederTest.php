<?php

namespace Tests\Feature;

use App\Models\AboutPage;
use App\Models\Banner;
use App\Models\Post;
use App\Models\Service;
use App\Models\SocialLink;
use App\Models\User;
use App\Support\SocialPlatformUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_installs_complete_repeatable_public_content_without_demo_users(): void
    {
        Storage::fake('public');

        Artisan::call('db:seed', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);

        $this->assertDatabaseCount('banners', 1);
        $this->assertDatabaseCount('services', 4);
        $this->assertDatabaseCount('about_pages', 1);
        $this->assertDatabaseCount('categories', 3);
        $this->assertDatabaseCount('posts', 3);
        $this->assertDatabaseCount('videos', 2);
        $this->assertDatabaseCount('team_members', 3);
        $this->assertDatabaseCount('testimonials', 3);
        $this->assertDatabaseCount('social_links', 3);
        $this->assertSame(0, User::query()->count());

        $this->assertSame(3, Post::query()->published()->count());
        $this->assertSame(4, Service::query()->where('active', true)->count());
        $this->assertTrue(Banner::query()->firstOrFail()->active);
        $this->assertTrue(AboutPage::query()->firstOrFail()->active);
        $this->assertSame(['x', 'linkedin', 'github'], SocialLink::query()->orderBy('position')->pluck('platform')->all());
        $this->assertTrue(app(SocialPlatformUrl::class)->valid('github', 'https://github.com/usf-tech-solutions'));

        foreach (['usf-tech-hero.svg', 'team-valentina.svg', 'team-andres.svg', 'team-camila.svg'] as $image) {
            Storage::disk('public')->assertExists('seed/'.$image);
            $this->assertStringContainsString('<svg', Storage::disk('public')->get('seed/'.$image));
        }
    }

    public function test_homepage_renders_seeded_company_sections(): void
    {
        Artisan::call('db:seed', ['--force' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Tecnología que impulsa lo que sigue')
            ->assertSeeText('Desarrollo a medida')
            ->assertSeeText('Nuestra misión')
            ->assertSeeText('Lo último en tecnología')
            ->assertSeeText('Conoce nuestras ideas')
            ->assertSeeText('Valentina Ríos')
            ->assertSeeText('Mariana López')
            ->assertSeeText('GitHub');
    }
}

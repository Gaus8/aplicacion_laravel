<?php

namespace Tests\Feature;

use App\Models\SocialLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialLinksModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_manage_social_links(): void
    {
        $this->get(route('admin.social-links.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_manage_platform_links_and_only_safe_active_urls_are_public(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.social-links.create'))->assertOk();
        $payload = ['platform' => 'instagram', 'label' => 'Instagram', 'url' => 'https://www.instagram.com/example/', 'position' => 0, 'active' => '1'];
        $this->post(route('admin.social-links.store'), $payload)->assertRedirect(route('admin.social-links.index'));
        $link = SocialLink::query()->firstOrFail();
        $this->assertSame($user->id, $link->creator->id);

        $response = $this->get(route('home'))->assertOk()->assertSee('href="'.$link->url.'"', false)->assertSeeText('Instagram');
        $this->assertSame(2, substr_count($response->getContent(), 'href="'.$link->url.'"'));

        $this->put(route('admin.social-links.update', $link), array_replace($payload, ['active' => '0']))->assertRedirect(route('admin.social-links.index'));
        $this->get(route('home'))->assertDontSee($link->url, false);
        $this->delete(route('admin.social-links.destroy', $link))->assertRedirect(route('admin.social-links.index'));
        $this->assertDatabaseMissing('social_links', ['id' => $link->id]);
    }

    public function test_url_must_use_the_selected_platform_official_https_domain(): void
    {
        $this->actingAs(User::factory()->create())->from(route('admin.social-links.create'))
            ->post(route('admin.social-links.store'), ['platform' => 'instagram', 'label' => 'Instagram', 'url' => 'https://instagram.com.attacker.test/account', 'position' => 0])
            ->assertRedirect(route('admin.social-links.create'))->assertSessionHasErrors('url');
        $this->assertDatabaseCount('social_links', 0);
    }
}

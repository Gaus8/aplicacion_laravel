<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_added_to_responses(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), geolocation=(), microphone=()')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
    }

    public function test_https_responses_include_hsts(): void
    {
        $this->get('https://localhost/')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_admin_dashboard_is_not_cached_and_dashboard_link_is_active(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertHeader('Pragma', 'no-cache')
            ->assertSee('aria-current="page"', false)
            ->assertSeeText('Dashboard');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
    }

    public function test_api_login_is_throttled_after_five_attempts(): void
    {
        foreach (range(1, 5) as $_) {
            $this->postJson('/api/login', [
                'email' => 'unknown@example.test',
                'password' => 'incorrect-password',
            ])->assertUnauthorized();
        }

        $this->postJson('/api/login', [
            'email' => 'unknown@example.test',
            'password' => 'incorrect-password',
        ])->assertTooManyRequests();
    }
}

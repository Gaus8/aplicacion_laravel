<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
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
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeader('Cross-Origin-Resource-Policy', 'same-origin')
            ->assertHeader('X-Permitted-Cross-Domain-Policies', 'none')
            ->assertHeader('X-DNS-Prefetch-Control', 'off');

        $policy = $this->get(route('home'))->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString("frame-ancestors 'none'", $policy);
        $this->assertStringNotContainsString("script-src 'self' 'unsafe-inline'", $policy);
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
        $response->assertSee('href="'.route('admin.seo.edit').'"', false)
            ->assertSee('href="'.route('admin.users.index').'"', false)
            ->assertSee('href="'.route('admin.roles.index').'"', false);

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

    public function test_production_readiness_command_reports_environment_gaps_without_secrets(): void
    {
        $this->assertSame(1, Artisan::call('cms:check-deployment'));
        $output = Artisan::output();
        $this->assertStringContainsString('APP_ENV es production', $output);
        if (filled(config('app.key'))) $this->assertStringNotContainsString((string) config('app.key'), $output);
    }

    public function test_session_cookie_defaults_are_http_only_and_same_site_lax(): void
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertSame('lax', config('session.same_site'));
    }
}

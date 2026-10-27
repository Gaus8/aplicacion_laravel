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
        $response = $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), geolocation=(), microphone=()')
            ->assertHeader('Cross-Origin-Resource-Policy', 'same-origin')
            ->assertHeader('X-Permitted-Cross-Domain-Policies', 'none')
            ->assertHeader('X-DNS-Prefetch-Control', 'off')
            ->assertCookieMissing('XSRF-TOKEN');

        $policy = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString("frame-ancestors 'none'", $policy);
        $this->assertStringContainsString("script-src-attr 'none'", $policy);
        $this->assertStringContainsString("style-src-attr 'none'", $policy);
        $this->assertStringNotContainsString('unsafe-inline', $policy);
        $this->assertStringNotContainsString('unsafe-eval', $policy);
        $this->assertStringNotContainsString(' https:;', $policy);
        $this->assertStringNotContainsString('*', $policy);
        $this->assertFalse($response->headers->has('X-Powered-By'));
        preg_match('/<meta property="csp-nonce" nonce="([^"]+)"/', $response->getContent(), $nonceMatch);
        $this->assertNotEmpty($nonceMatch[1] ?? null);
        $this->assertStringContainsString("'nonce-{$nonceMatch[1]}'", $policy);
    }

    public function test_security_headers_are_also_added_to_forbidden_responses(): void
    {
        $user = User::factory()->create();
        $user->roles()->detach();

        $response = $this->actingAs($user)->get(route('dashboard'))->assertForbidden();

        $this->assertSame('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertNotEmpty($response->headers->get('Content-Security-Policy'));
    }

    public function test_api_does_not_allow_cross_origin_reads(): void
    {
        $response = $this->withHeader('Origin', 'https://attacker.example')->getJson('/api/dashboard');

        $this->assertFalse($response->headers->has('Access-Control-Allow-Origin'));
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
        $this->assertStringNotContainsString('style="height:', $response->getContent());
    }

    public function test_api_login_is_throttled_after_five_attempts(): void
    {
        foreach (range(1, 5) as $_) {
            $this->postJson('/api/login', [
                'email' => 'unknown@example.test',
                'password' => 'incorrect-password',
            ])->assertUnauthorized();
        }

        $response = $this->postJson('/api/login', [
            'email' => 'unknown@example.test',
            'password' => 'incorrect-password',
        ])->assertTooManyRequests();

        $this->assertFalse($response->headers->has('X-RateLimit-Reset'));
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

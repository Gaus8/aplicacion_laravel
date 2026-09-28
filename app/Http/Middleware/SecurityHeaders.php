<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), geolocation=(), microphone=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('X-DNS-Prefetch-Control', 'off');

        $viteOrigins = app()->environment('local', 'testing') ? ' http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173' : '';
        $viteConnections = app()->environment('local', 'testing') ? ' ws://localhost:5173 ws://127.0.0.1:5173 ws://[::1]:5173' : '';
        $policy = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "script-src 'self'{$viteOrigins}",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com{$viteOrigins}",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: blob: https:",
            'frame-src https://www.youtube-nocookie.com https://player.vimeo.com',
            "connect-src 'self'{$viteOrigins}{$viteConnections}",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $policy));

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        if ($request->is('admin/*') || $request->is('login') || $request->is('password/*')) {
            $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        /** @var Response $response */
        $response = $next($request);

        return $this->apply($request, $response);
    }

    public function apply(Request $request, Response $response): Response
    {
        $nonce = Vite::cspNonce() ?? Vite::useCspNonce();

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), geolocation=(), microphone=()');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('X-DNS-Prefetch-Control', 'off');
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        $response->headers->remove('X-RateLimit-Reset');

        $viteIsHot = app()->environment('local', 'testing') && Vite::isRunningHot();
        $viteOrigins = $viteIsHot ? ' http://localhost:5173 http://127.0.0.1:5173' : '';
        $viteConnections = $viteIsHot ? ' ws://localhost:5173 ws://127.0.0.1:5173' : '';
        $policy = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "script-src 'self' 'nonce-{$nonce}'{$viteOrigins}",
            "script-src-elem 'self' 'nonce-{$nonce}'{$viteOrigins}",
            "script-src-attr 'none'",
            "style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com{$viteOrigins}",
            "style-src-elem 'self' 'nonce-{$nonce}' https://fonts.googleapis.com{$viteOrigins}",
            "style-src-attr 'none'",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: blob:",
            'frame-src https://www.youtube-nocookie.com https://player.vimeo.com',
            "connect-src 'self'{$viteOrigins}{$viteConnections}",
            "manifest-src 'self'",
            "worker-src 'self' blob:",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $policy));

        if (!headers_sent()) {
            header_remove('X-Powered-By');
            header_remove('Server');
        }

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

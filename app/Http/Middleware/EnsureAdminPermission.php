<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $permission = $this->permissionForRoute((string) $request->route()?->getName());
        if ($permission !== null && !$request->user()?->hasPermission($permission)) {
            abort(403);
        }

        return $next($request);
    }

    private function permissionForRoute(string $name): ?string
    {
        $map = [
            'admin.design-system' => 'dashboard.view',
            'api.dashboard' => 'dashboard.view',
            'dashboard' => 'dashboard.view', 'admin.dashboard' => 'dashboard.view',
            'admin.about.' => 'about.manage', 'admin.categories.' => 'categories.manage',
            'admin.posts.' => 'posts.manage', 'admin.videos.' => 'videos.manage',
            'admin.team.' => 'team.manage', 'admin.testimonials.' => 'testimonials.manage',
            'admin.contact-messages.' => 'contact.manage', 'admin.social-links.' => 'social.manage',
            'admin.banners.' => 'banners.manage', 'admin.services.' => 'services.manage',
            'admin.media.' => 'media.manage', 'admin.smtp.' => 'smtp.manage',
            'admin.audit.' => 'audit.view', 'admin.seo.' => 'seo.manage',
            'admin.users.' => 'users.manage', 'admin.roles.' => 'roles.manage',
            'admin.emails.' => 'email.manage',
        ];

        foreach ($map as $prefix => $permission) {
            if ($name === $prefix || str_starts_with($name, $prefix)) return $permission;
        }
        return null;
    }
}

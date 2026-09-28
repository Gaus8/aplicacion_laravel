<?php

namespace App\Support;

class SocialPlatformUrl
{
    private const HOSTS = [
        'facebook' => ['facebook.com', 'www.facebook.com'],
        'instagram' => ['instagram.com', 'www.instagram.com'],
        'linkedin' => ['linkedin.com', 'www.linkedin.com'],
        'tiktok' => ['tiktok.com', 'www.tiktok.com'],
        'x' => ['x.com', 'www.x.com', 'twitter.com', 'www.twitter.com'],
        'youtube' => ['youtube.com', 'www.youtube.com', 'youtu.be', 'www.youtu.be'],
        'whatsapp' => ['wa.me', 'api.whatsapp.com', 'www.whatsapp.com'],
        'github' => ['github.com', 'www.github.com'],
    ];

    public function valid(string $platform, string $url): bool
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');

        return ($parts['scheme'] ?? '') === 'https'
            && in_array($host, self::HOSTS[$platform] ?? [], true)
            && empty($parts['user'])
            && empty($parts['pass']);
    }
}

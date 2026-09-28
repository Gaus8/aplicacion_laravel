<?php

namespace App\Support;

class VideoUrlParser
{
    /** @return array{provider: string, id: string}|null */
    public function parse(string $url): ?array
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $id = explode('/', $path)[0] ?? '';

            return preg_match('/^[A-Za-z0-9_-]{11}$/', $id) ? ['provider' => 'youtube', 'id' => $id] : null;
        }

        if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
            parse_str($parts['query'] ?? '', $query);
            $segments = explode('/', $path);
            $id = match ($segments[0] ?? '') {
                'embed', 'shorts', 'live' => $segments[1] ?? '',
                default => $query['v'] ?? '',
            };

            return preg_match('/^[A-Za-z0-9_-]{11}$/', $id) ? ['provider' => 'youtube', 'id' => $id] : null;
        }

        if (in_array($host, ['vimeo.com', 'www.vimeo.com', 'player.vimeo.com'], true)) {
            $segments = explode('/', $path);
            $id = $host === 'player.vimeo.com' && ($segments[0] ?? '') === 'video'
                ? ($segments[1] ?? '')
                : ($segments[0] ?? '');

            return preg_match('/^[0-9]{1,12}$/', $id) ? ['provider' => 'vimeo', 'id' => $id] : null;
        }

        return null;
    }
}

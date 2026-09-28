<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class PublicSeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([route('home'), route('about.public'), route('posts.public.index'), route('videos.public.index'), route('team.public.index'), route('contact.create')])
            ->merge(Post::query()->published()->pluck('slug')->map(fn (string $slug) => route('posts.public.show', $slug)));
        return response(view('public.seo.sitemap', compact('urls')), 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        return response("User-agent: *\nDisallow: /admin/\nDisallow: /login\nSitemap: ".route('seo.sitemap')."\n", 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}

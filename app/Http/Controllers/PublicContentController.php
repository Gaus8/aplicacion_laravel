<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use App\Models\Post;
use App\Models\TeamMember;
use App\Models\Video;
use Illuminate\View\View;

class PublicContentController extends Controller
{
    public function about(): View
    {
        $page = AboutPage::query()->where('active', true)->firstOrFail();

        return view('public.about', compact('page'));
    }

    public function posts(): View
    {
        $posts = Post::query()->published()->with(['category', 'author'])->latest('published_at')->paginate(9);

        return view('public.posts.index', compact('posts'));
    }

    public function post(Post $post): View
    {
        abort_unless($post->status === 'published' && (!$post->published_at || $post->published_at->isPast()), 404);
        $post->load(['category', 'author']);

        return view('public.posts.show', compact('post'));
    }

    public function videos(): View
    {
        $videos = Video::query()->where('active', true)->orderBy('position')->latest('id')->paginate(9);

        return view('public.videos.index', compact('videos'));
    }

    public function team(): View
    {
        $members = TeamMember::query()->where('active', true)->orderBy('position')->latest('id')->get();

        return view('public.team.index', compact('members'));
    }
}

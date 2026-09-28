<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use App\Models\Banner;
use App\Models\Post;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Video;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $banners = Banner::query()->published()->orderBy('position')->latest('id')->get();
        $services = Service::query()->where('active', true)->orderBy('position')->latest('id')->get();
        $testimonials = Testimonial::query()->where('active', true)->orderBy('position')->latest('id')->get();
        $aboutPage = AboutPage::query()->where('active', true)->first();
        $posts = Post::query()->published()->with('category')->latest('published_at')->limit(3)->get();
        $videos = Video::query()->where('active', true)->orderBy('position')->latest('id')->limit(2)->get();
        $teamMembers = TeamMember::query()->where('active', true)->orderBy('position')->limit(3)->get();

        return view('home', compact('aboutPage', 'banners', 'posts', 'services', 'teamMembers', 'testimonials', 'videos'));
    }
}

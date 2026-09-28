<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $banners = Banner::query()->published()->orderBy('position')->latest('id')->get();
        $services = Service::query()->where('active', true)->orderBy('position')->latest('id')->get();

        return view('home', compact('banners', 'services'));
    }
}

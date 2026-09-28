<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $banners = Banner::query()->published()->orderBy('position')->latest('id')->get();

        return view('home', compact('banners'));
    }
}

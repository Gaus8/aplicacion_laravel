<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSeoSettingRequest;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SeoSettingController extends Controller
{
    public function edit()
    {
        Gate::authorize('viewAny', SeoSetting::class);
        return view('admin.seo.edit', ['setting' => SeoSetting::query()->firstOrCreate(['id' => 1], [
            'site_name' => config('app.name'), 'title_template' => '%s | '.config('app.name'),
            'default_title' => config('app.name'), 'default_description' => 'Sitio oficial', 'robots_directive' => 'index,follow',
        ])]);
    }

    public function update(UpdateSeoSettingRequest $request)
    {
        $data = $request->validated();
        $setting = SeoSetting::query()->firstOrCreate(['id' => 1], ['site_name' => config('app.name'), 'title_template' => '%s | '.config('app.name'), 'default_title' => config('app.name'), 'default_description' => 'Sitio oficial']);
        $oldImage = $setting->og_image_path;
        $newImage = $request->hasFile('og_image') ? $request->file('og_image')->store('seo', 'public') : $oldImage;
        $setting->fill([
            'site_name' => $data['site_name'], 'title_template' => $data['title_template'],
            'default_title' => $data['default_title'], 'default_description' => $data['default_description'],
            'canonical_base_url' => $data['canonical_base_url'] ?? null, 'robots_directive' => $data['robots_directive'],
            'og_image_path' => $newImage,
        ])->save();
        if ($newImage !== $oldImage && $oldImage) Storage::disk('public')->delete($oldImage);
        return back()->with('success', 'La configuración SEO se guardó.');
    }
}

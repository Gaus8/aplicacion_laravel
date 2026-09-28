<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAboutPageRequest;
use App\Models\AboutPage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AboutPageController extends Controller
{
    public function edit()
    {
        Gate::authorize('viewAny', AboutPage::class);
        $page = AboutPage::query()->firstOrNew(['id' => 1]);

        return view('admin.about.form', compact('page'));
    }

    public function update(UpdateAboutPageRequest $request)
    {
        $data = $request->validated();
        $page = AboutPage::query()->firstOrNew(['id' => 1]);
        $oldImage = $page->image_path;
        $newImage = $request->hasFile('image') ? $request->file('image')->store('about', 'public') : $oldImage;

        try {
            $page->fill([
                'title' => trim($data['title']),
                'subtitle' => isset($data['subtitle']) ? trim($data['subtitle']) : null,
                'body' => trim($data['body']),
                'mission' => isset($data['mission']) ? trim($data['mission']) : null,
                'vision' => isset($data['vision']) ? trim($data['vision']) : null,
                'image_path' => $newImage,
                'image_alt' => isset($data['image_alt']) ? trim($data['image_alt']) : $page->image_alt,
                'active' => (bool) ($data['active'] ?? false),
            ]);
            $page->created_by ??= $request->user()->getKey();
            $page->updated_by = $request->user()->getKey();
            $page->save();
        } catch (Throwable $exception) {
            if ($newImage !== $oldImage) Storage::disk('public')->delete($newImage);
            throw $exception;
        }

        if ($newImage !== $oldImage && $oldImage) Storage::disk('public')->delete($oldImage);

        return redirect()->route('admin.about.edit')->with('success', 'La información institucional se guardó correctamente.');
    }
}

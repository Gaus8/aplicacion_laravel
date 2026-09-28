<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexBannerRequest;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BannerController extends Controller
{
    public function index(IndexBannerRequest $request)
    {
        $filters = $request->validated();
        $banners = Banner::query()
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('title', 'like', '%'.trim($search).'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('active', $status === 'active'))
            ->orderBy('position')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.banners.index', compact('banners', 'filters'));
    }

    public function create()
    {
        Gate::authorize('create', Banner::class);

        return view('admin.banners.form', ['banner' => new Banner(), 'isEditing' => false]);
    }

    public function store(StoreBannerRequest $request)
    {
        $validated = $request->validated();
        $imagePath = $request->file('image')->store('banners', 'public');

        try {
            $banner = new Banner();
            $banner->fill($this->attributes($validated, $imagePath));
            $banner->created_by = $request->user()->getKey();
            $banner->updated_by = $request->user()->getKey();
            $banner->save();
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($imagePath);
            throw $exception;
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner creado correctamente.');
    }

    public function edit(Banner $banner)
    {
        Gate::authorize('update', $banner);

        return view('admin.banners.form', ['banner' => $banner, 'isEditing' => true]);
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $validated = $request->validated();
        $oldImagePath = $banner->image_path;
        $newImagePath = $request->hasFile('image')
            ? $request->file('image')->store('banners', 'public')
            : $oldImagePath;

        try {
            $banner->fill($this->attributes($validated, $newImagePath));
            $banner->updated_by = $request->user()->getKey();
            $banner->save();
        } catch (Throwable $exception) {
            if ($newImagePath !== $oldImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }
            throw $exception;
        }

        if ($newImagePath !== $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner actualizado correctamente.');
    }

    public function destroy(Banner $banner)
    {
        Gate::authorize('delete', $banner);
        $imagePath = $banner->image_path;
        $banner->delete();
        Storage::disk('public')->delete($imagePath);

        return redirect()->route('admin.banners.index')->with('success', 'Banner eliminado correctamente.');
    }

    private function attributes(array $validated, string $imagePath): array
    {
        return [
            'title' => trim($validated['title']),
            'subtitle' => isset($validated['subtitle']) ? trim($validated['subtitle']) : null,
            'image_path' => $imagePath,
            'image_alt' => trim($validated['image_alt']),
            'cta_label' => isset($validated['cta_label']) ? trim($validated['cta_label']) : null,
            'cta_url' => isset($validated['cta_url']) ? trim($validated['cta_url']) : null,
            'position' => $validated['position'],
            'active' => (bool) ($validated['active'] ?? false),
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexServiceRequest;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(IndexServiceRequest $request)
    {
        $filters = $request->validated();
        $services = Service::query()
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $term = '%'.trim($search).'%';
                $query->where(fn ($query) => $query->where('title', 'like', $term)->orWhere('summary', 'like', $term));
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('active', $status === 'active'))
            ->orderBy('position')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.services.index', compact('services', 'filters'));
    }

    public function create()
    {
        Gate::authorize('create', Service::class);

        return view('admin.services.form', ['service' => new Service(), 'isEditing' => false]);
    }

    public function store(StoreServiceRequest $request)
    {
        $validated = $request->validated();
        $service = new Service();
        $service->fill($this->attributes($validated));
        $service->slug = $this->uniqueSlug($validated['title']);
        $service->created_by = $request->user()->getKey();
        $service->updated_by = $request->user()->getKey();
        $service->save();

        return redirect()->route('admin.services.index')->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Service $service)
    {
        Gate::authorize('update', $service);

        return view('admin.services.form', ['service' => $service, 'isEditing' => true]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $validated = $request->validated();
        $service->fill($this->attributes($validated));
        if ($service->isDirty('title')) {
            $service->slug = $this->uniqueSlug($validated['title'], $service->getKey());
        }
        $service->updated_by = $request->user()->getKey();
        $service->save();

        return redirect()->route('admin.services.index')->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Service $service)
    {
        Gate::authorize('delete', $service);
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Servicio eliminado correctamente.');
    }

    private function attributes(array $validated): array
    {
        return [
            'title' => trim($validated['title']),
            'summary' => trim($validated['summary']),
            'description' => trim($validated['description']),
            'cta_label' => isset($validated['cta_label']) ? trim($validated['cta_label']) : null,
            'cta_url' => isset($validated['cta_url']) ? trim($validated['cta_url']) : null,
            'position' => $validated['position'],
            'active' => (bool) ($validated['active'] ?? false),
        ];
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'servicio';
        $slug = $baseSlug;
        $suffix = 2;

        while (Service::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }
}

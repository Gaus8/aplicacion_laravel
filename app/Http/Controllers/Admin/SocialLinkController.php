<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexSocialLinkRequest;
use App\Http\Requests\Admin\StoreSocialLinkRequest;
use App\Http\Requests\Admin\UpdateSocialLinkRequest;
use App\Models\SocialLink;
use Illuminate\Support\Facades\Gate;

class SocialLinkController extends Controller
{
    public function index(IndexSocialLinkRequest $request)
    {
        $filters = $request->validated();
        $links = SocialLink::query()
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('label', 'like', '%'.trim($search).'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('active', $status === 'active'))
            ->orderBy('position')->latest('id')->paginate(15)->withQueryString();

        return view('admin.social-links.index', compact('links', 'filters'));
    }

    public function create()
    {
        Gate::authorize('create', SocialLink::class);

        return view('admin.social-links.form', ['link' => new SocialLink(), 'isEditing' => false]);
    }

    public function store(StoreSocialLinkRequest $request)
    {
        $link = new SocialLink();
        $link->fill($this->attributes($request->validated()));
        $link->created_by = $request->user()->getKey();
        $link->updated_by = $request->user()->getKey();
        $link->save();

        return redirect()->route('admin.social-links.index')->with('success', 'Enlace social guardado correctamente.');
    }

    public function edit(SocialLink $social_link)
    {
        Gate::authorize('update', $social_link);

        return view('admin.social-links.form', ['link' => $social_link, 'isEditing' => true]);
    }

    public function update(UpdateSocialLinkRequest $request, SocialLink $social_link)
    {
        $social_link->fill($this->attributes($request->validated()));
        $social_link->updated_by = $request->user()->getKey();
        $social_link->save();

        return redirect()->route('admin.social-links.index')->with('success', 'Enlace social actualizado correctamente.');
    }

    public function destroy(SocialLink $social_link)
    {
        Gate::authorize('delete', $social_link);
        $social_link->delete();

        return redirect()->route('admin.social-links.index')->with('success', 'Enlace social eliminado correctamente.');
    }

    private function attributes(array $data): array
    {
        return [
            'platform' => $data['platform'], 'label' => trim($data['label']), 'url' => trim($data['url']),
            'position' => $data['position'], 'active' => (bool) ($data['active'] ?? false),
        ];
    }
}

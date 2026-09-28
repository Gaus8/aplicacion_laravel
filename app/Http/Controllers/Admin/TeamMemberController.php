<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexTeamMemberRequest;
use App\Http\Requests\Admin\StoreTeamMemberRequest;
use App\Http\Requests\Admin\UpdateTeamMemberRequest;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TeamMemberController extends Controller
{
    public function index(IndexTeamMemberRequest $request)
    {
        $filters = $request->validated();
        $members = TeamMember::query()
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $term = '%'.trim($search).'%';
                $query->where(fn ($query) => $query->where('name', 'like', $term)->orWhere('role', 'like', $term));
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('active', $status === 'active'))
            ->orderBy('position')->latest('id')->paginate(15)->withQueryString();

        return view('admin.team.index', compact('members', 'filters'));
    }

    public function create()
    {
        Gate::authorize('create', TeamMember::class);

        return view('admin.team.form', ['member' => new TeamMember(), 'isEditing' => false]);
    }

    public function store(StoreTeamMemberRequest $request)
    {
        $data = $request->validated();
        $imagePath = $request->hasFile('image') ? $request->file('image')->store('team', 'public') : null;

        try {
            $member = new TeamMember();
            $member->fill($this->attributes($data, $imagePath));
            $member->created_by = $request->user()->getKey();
            $member->updated_by = $request->user()->getKey();
            $member->save();
        } catch (Throwable $exception) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            throw $exception;
        }

        return redirect()->route('admin.team.index')->with('success', 'Miembro agregado correctamente.');
    }

    public function edit(TeamMember $team_member)
    {
        Gate::authorize('update', $team_member);

        return view('admin.team.form', ['member' => $team_member, 'isEditing' => true]);
    }

    public function update(UpdateTeamMemberRequest $request, TeamMember $team_member)
    {
        $data = $request->validated();
        $oldImage = $team_member->image_path;
        $newImage = $request->hasFile('image') ? $request->file('image')->store('team', 'public') : $oldImage;

        try {
            $team_member->fill($this->attributes($data, $newImage));
            $team_member->updated_by = $request->user()->getKey();
            $team_member->save();
        } catch (Throwable $exception) {
            if ($newImage !== $oldImage && $newImage) Storage::disk('public')->delete($newImage);
            throw $exception;
        }

        if ($newImage !== $oldImage && $oldImage) Storage::disk('public')->delete($oldImage);

        return redirect()->route('admin.team.index')->with('success', 'Miembro actualizado correctamente.');
    }

    public function destroy(TeamMember $team_member)
    {
        Gate::authorize('delete', $team_member);
        $image = $team_member->image_path;
        $team_member->delete();
        if ($image) Storage::disk('public')->delete($image);

        return redirect()->route('admin.team.index')->with('success', 'Miembro eliminado correctamente.');
    }

    private function attributes(array $data, ?string $image): array
    {
        return [
            'name' => trim($data['name']), 'role' => trim($data['role']), 'bio' => trim($data['bio']),
            'email' => $data['email'] ?? null, 'profile_url' => $data['profile_url'] ?? null,
            'image_path' => $image, 'image_alt' => $data['image_alt'] ?? null,
            'position' => $data['position'], 'active' => (bool) ($data['active'] ?? false),
        ];
    }
}

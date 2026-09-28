<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexVideoRequest;
use App\Http\Requests\Admin\StoreVideoRequest;
use App\Http\Requests\Admin\UpdateVideoRequest;
use App\Models\Video;
use App\Support\VideoUrlParser;
use Illuminate\Support\Facades\Gate;

class VideoController extends Controller
{
    public function index(IndexVideoRequest $request)
    {
        $filters = $request->validated();
        $videos = Video::query()
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('title', 'like', '%'.trim($search).'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('active', $status === 'active'))
            ->orderBy('position')->latest('id')->paginate(15)->withQueryString();

        return view('admin.videos.index', compact('videos', 'filters'));
    }

    public function create()
    {
        Gate::authorize('create', Video::class);

        return view('admin.videos.form', ['video' => new Video(), 'isEditing' => false]);
    }

    public function store(StoreVideoRequest $request, VideoUrlParser $parser)
    {
        $data = $request->validated();
        $parsed = $parser->parse($data['video_url']);
        $video = new Video();
        $video->fill([
            'title' => trim($data['title']), 'description' => isset($data['description']) ? trim($data['description']) : null,
            'provider' => $parsed['provider'], 'external_id' => $parsed['id'], 'video_url' => trim($data['video_url']),
            'position' => $data['position'], 'active' => (bool) ($data['active'] ?? false),
            'created_by' => $request->user()->getKey(), 'updated_by' => $request->user()->getKey(),
        ])->save();

        return redirect()->route('admin.videos.index')->with('success', 'Video guardado correctamente.');
    }

    public function edit(Video $video)
    {
        Gate::authorize('update', $video);

        return view('admin.videos.form', ['video' => $video, 'isEditing' => true]);
    }

    public function update(UpdateVideoRequest $request, Video $video, VideoUrlParser $parser)
    {
        $data = $request->validated();
        $parsed = $parser->parse($data['video_url']);
        $video->fill([
            'title' => trim($data['title']), 'description' => isset($data['description']) ? trim($data['description']) : null,
            'provider' => $parsed['provider'], 'external_id' => $parsed['id'], 'video_url' => trim($data['video_url']),
            'position' => $data['position'], 'active' => (bool) ($data['active'] ?? false), 'updated_by' => $request->user()->getKey(),
        ])->save();

        return redirect()->route('admin.videos.index')->with('success', 'Video actualizado correctamente.');
    }

    public function destroy(Video $video)
    {
        Gate::authorize('delete', $video);
        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Video eliminado correctamente.');
    }
}

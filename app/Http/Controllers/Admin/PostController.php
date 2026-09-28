<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexPostRequest;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Support\UniqueSlug;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PostController extends Controller
{
    public function index(IndexPostRequest $request)
    {
        $filters = $request->validated();
        $posts = Post::query()->with(['category', 'author'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $term = '%'.trim($search).'%';
                $query->where(fn ($query) => $query->where('title', 'like', $term)->orWhere('excerpt', 'like', $term));
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest('updated_at')->paginate(15)->withQueryString();

        return view('admin.posts.index', compact('posts', 'filters'));
    }

    public function create()
    {
        Gate::authorize('create', Post::class);
        $categories = Category::query()->where('active', true)->orderBy('name')->get();

        return view('admin.posts.form', ['post' => new Post(), 'categories' => $categories, 'isEditing' => false]);
    }

    public function store(StorePostRequest $request, UniqueSlug $slugs)
    {
        $data = $request->validated();
        $coverPath = $request->hasFile('cover') ? $request->file('cover')->store('posts', 'public') : null;

        try {
            $post = new Post();
            $post->fill($this->attributes($data, $coverPath));
            $post->slug = $slugs->make(Post::class, $data['title']);
            $post->author_id = $request->user()->getKey();
            $post->updated_by = $request->user()->getKey();
            $post->published_at = $data['published_at'] ?? ($data['status'] === 'published' ? now() : null);
            $post->save();
        } catch (Throwable $exception) {
            if ($coverPath) Storage::disk('public')->delete($coverPath);
            throw $exception;
        }

        return redirect()->route('admin.posts.index')->with('success', 'Publicación guardada correctamente.');
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.posts.form', compact('post', 'categories') + ['isEditing' => true]);
    }

    public function update(UpdatePostRequest $request, Post $post, UniqueSlug $slugs)
    {
        $data = $request->validated();
        $oldCover = $post->cover_path;
        $newCover = $request->hasFile('cover') ? $request->file('cover')->store('posts', 'public') : $oldCover;

        try {
            $post->fill($this->attributes($data, $newCover));
            if ($post->isDirty('title')) $post->slug = $slugs->make(Post::class, $data['title'], $post->getKey());
            $post->updated_by = $request->user()->getKey();
            $post->published_at = $data['published_at'] ?? ($data['status'] === 'published' ? ($post->published_at ?? now()) : null);
            $post->save();
        } catch (Throwable $exception) {
            if ($newCover !== $oldCover && $newCover) Storage::disk('public')->delete($newCover);
            throw $exception;
        }

        if ($newCover !== $oldCover && $oldCover) Storage::disk('public')->delete($oldCover);

        return redirect()->route('admin.posts.index')->with('success', 'Publicación actualizada correctamente.');
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);
        $cover = $post->cover_path;
        $post->delete();
        if ($cover) Storage::disk('public')->delete($cover);

        return redirect()->route('admin.posts.index')->with('success', 'Publicación eliminada correctamente.');
    }

    private function attributes(array $data, ?string $cover): array
    {
        return [
            'title' => trim($data['title']),
            'excerpt' => trim($data['excerpt']),
            'body' => trim($data['body']),
            'category_id' => $data['category_id'],
            'status' => $data['status'],
            'cover_path' => $cover,
            'cover_alt' => $data['cover_alt'] ?? null,
        ];
    }
}

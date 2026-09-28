<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexCategoryRequest;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Support\UniqueSlug;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index(IndexCategoryRequest $request)
    {
        $filters = $request->validated();
        $categories = Category::query()
            ->withCount('posts')
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', '%'.trim($search).'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('active', $status === 'active'))
            ->latest('id')->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories', 'filters'));
    }

    public function create()
    {
        Gate::authorize('create', Category::class);

        return view('admin.categories.form', ['category' => new Category(), 'isEditing' => false]);
    }

    public function store(StoreCategoryRequest $request, UniqueSlug $slugs)
    {
        $data = $request->validated();
        $category = new Category();
        $category->fill([
            'name' => trim($data['name']),
            'slug' => $slugs->make(Category::class, $data['name']),
            'description' => isset($data['description']) ? trim($data['description']) : null,
            'active' => (bool) ($data['active'] ?? false),
            'created_by' => $request->user()->getKey(),
            'updated_by' => $request->user()->getKey(),
        ])->save();

        return redirect()->route('admin.categories.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Category $category)
    {
        Gate::authorize('update', $category);

        return view('admin.categories.form', ['category' => $category, 'isEditing' => true]);
    }

    public function update(UpdateCategoryRequest $request, Category $category, UniqueSlug $slugs)
    {
        $data = $request->validated();
        $category->name = trim($data['name']);
        if ($category->isDirty('name')) $category->slug = $slugs->make(Category::class, $data['name'], $category->getKey());
        $category->description = isset($data['description']) ? trim($data['description']) : null;
        $category->active = (bool) ($data['active'] ?? false);
        $category->updated_by = $request->user()->getKey();
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category)
    {
        Gate::authorize('delete', $category);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Categoría eliminada correctamente.');
    }
}

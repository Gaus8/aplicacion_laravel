<?php

namespace App\Http\Requests\Admin;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', Post::class) ?? false; }
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:180'],
            'excerpt' => ['required', 'string', 'max:320'],
            'body' => ['required', 'string', 'max:50000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'status' => ['required', 'in:draft,review,published,archived'],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(5120)],
            'cover_alt' => ['nullable', 'required_with:cover', 'string', 'max:180'],
        ];
    }
}

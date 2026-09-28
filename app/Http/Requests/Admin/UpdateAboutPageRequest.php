<?php

namespace App\Http\Requests\Admin;

use App\Models\AboutPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateAboutPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', new AboutPage()) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:140'],
            'subtitle' => ['nullable', 'string', 'max:240'],
            'body' => ['required', 'string', 'max:30000'],
            'mission' => ['nullable', 'string', 'max:5000'],
            'vision' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(5120)],
            'image_alt' => ['nullable', 'required_with:image', 'string', 'max:180'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}

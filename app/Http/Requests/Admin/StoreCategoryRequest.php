<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', Category::class) ?? false; }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120', Rule::unique('categories', 'name')],
            'description' => ['nullable', 'string', 'max:1000'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class IndexCategoryRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('viewAny', Category::class) ?? false; }
    public function rules(): array { return ['search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:active,inactive']]; }
}

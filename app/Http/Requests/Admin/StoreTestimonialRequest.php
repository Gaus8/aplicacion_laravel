<?php

namespace App\Http\Requests\Admin;

use App\Models\Testimonial;
use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', Testimonial::class) ?? false; }
    public function rules(): array
    {
        return [
            'person_name' => ['required', 'string', 'min:2', 'max:140'],
            'role' => ['nullable', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:140'],
            'quote' => ['required', 'string', 'min:10', 'max:5000'],
            'position' => ['required', 'integer', 'min:0', 'max:10000'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}

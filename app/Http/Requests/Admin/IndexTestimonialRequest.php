<?php

namespace App\Http\Requests\Admin;

use App\Models\Testimonial;
use Illuminate\Foundation\Http\FormRequest;

class IndexTestimonialRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('viewAny', Testimonial::class) ?? false; }
    public function rules(): array { return ['search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:active,inactive']]; }
}

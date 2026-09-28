<?php

namespace App\Http\Requests\Admin;

use App\Models\Video;
use Illuminate\Foundation\Http\FormRequest;

class IndexVideoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('viewAny', Video::class) ?? false; }
    public function rules(): array { return ['search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:active,inactive']]; }
}

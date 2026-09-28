<?php

namespace App\Http\Requests\Admin;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class IndexServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Service::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }
}

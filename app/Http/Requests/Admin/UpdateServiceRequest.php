<?php

namespace App\Http\Requests\Admin;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $service = $this->route('service');

        return $service instanceof Service && ($this->user()?->can('update', $service) ?? false);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'summary' => ['required', 'string', 'max:240'],
            'description' => ['required', 'string', 'max:10000'],
            'cta_label' => ['nullable', 'required_with:cta_url', 'string', 'max:60'],
            'cta_url' => ['nullable', 'required_with:cta_label', 'url:http,https', 'max:2048'],
            'position' => ['required', 'integer', 'min:0', 'max:10000'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->filled('cta_label') xor $this->filled('cta_url')) {
                $validator->errors()->add('cta_label', 'Completa el texto y la URL del enlace, o deja ambos vacíos.');
            }
        }];
    }
}

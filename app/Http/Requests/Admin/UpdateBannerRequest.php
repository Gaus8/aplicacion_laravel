<?php

namespace App\Http\Requests\Admin;

use App\Models\Banner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $banner = $this->route('banner');

        return $banner instanceof Banner && ($this->user()?->can('update', $banner) ?? false);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:80'],
            'subtitle' => ['nullable', 'string', 'max:180'],
            'image' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(5120)],
            'image_alt' => ['required', 'string', 'max:180'],
            'cta_label' => ['nullable', 'required_with:cta_url', 'string', 'max:40'],
            'cta_url' => ['nullable', 'required_with:cta_label', 'url:http,https', 'max:2048'],
            'position' => ['required', 'integer', 'min:0', 'max:10000'],
            'active' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->filled('cta_label') xor $this->filled('cta_url')) {
                $validator->errors()->add('cta_label', 'Completa el texto y la URL del botón, o deja ambos vacíos.');
            }
        }];
    }
}

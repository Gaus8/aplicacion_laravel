<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSeoSettingRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasPermission('seo.manage') ?? false; }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:120'],
            'title_template' => ['required', 'string', 'max:180', 'regex:/^[^%]*%s[^%]*$/'],
            'default_title' => ['required', 'string', 'max:180'],
            'default_description' => ['required', 'string', 'max:320'],
            'canonical_base_url' => ['nullable', 'url:https', 'max:255'],
            'robots_directive' => ['required', Rule::in(['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'])],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}

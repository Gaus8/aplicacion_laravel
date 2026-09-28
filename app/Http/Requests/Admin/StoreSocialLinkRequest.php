<?php

namespace App\Http\Requests\Admin;

use App\Models\SocialLink;
use App\Support\SocialPlatformUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSocialLinkRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', SocialLink::class) ?? false; }
    public function rules(): array
    {
        return [
            'platform' => ['required', 'in:'.implode(',', SocialLink::PLATFORMS)],
            'label' => ['required', 'string', 'max:80'],
            'url' => ['required', 'url:https', 'max:2048'],
            'position' => ['required', 'integer', 'min:0', 'max:10000'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
    public function after(): array
    {
        return [function (Validator $validator): void {
            if (is_string($this->input('platform')) && is_string($this->input('url')) && $this->filled('platform') && $this->filled('url') && !app(SocialPlatformUrl::class)->valid($this->input('platform'), $this->input('url'))) {
                $validator->errors()->add('url', 'El enlace debe pertenecer al sitio oficial de la red seleccionada.');
            }
        }];
    }
}

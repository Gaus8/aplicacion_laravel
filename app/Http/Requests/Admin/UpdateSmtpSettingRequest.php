<?php

namespace App\Http\Requests\Admin;

use App\Models\SmtpSetting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSmtpSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $setting = SmtpSetting::query()->first();
        return $setting
            ? ($this->user()?->can('update', $setting) ?? false)
            : ($this->user()?->can('viewAny', SmtpSetting::class) ?? false);
    }

    public function rules(): array
    {
        return [
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'encryption' => ['nullable', 'in:tls,ssl'],
            'authentication_required' => ['sometimes', 'boolean'],
            'username' => ['nullable', 'string', 'max:255', 'required_if:authentication_required,1'],
            'password' => ['nullable', 'string', 'max:4096'],
            'from_address' => ['required', 'email', 'max:255'],
            'from_name' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

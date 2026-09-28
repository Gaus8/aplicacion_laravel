<?php

namespace App\Http\Requests\Admin;

use App\Models\SmtpSetting;
use Illuminate\Foundation\Http\FormRequest;

class TestSmtpSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('test', SmtpSetting::class) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}

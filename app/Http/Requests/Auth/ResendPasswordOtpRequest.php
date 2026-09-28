<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResendPasswordOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return filled($this->session()->get('password_recovery.email'));
    }

    public function rules(): array { return []; }
}

<?php

namespace App\Http\Requests\Auth;

use App\Models\PasswordRecoveryOtp;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordWithOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        $email = $this->session()->get('password_recovery.email');

        return filled($email)
            && $this->session()->get('password_recovery.verified') === true
            && PasswordRecoveryOtp::query()
                ->where('email', $email)
                ->whereNotNull('verified_at')
                ->where('expires_at', '>', now())
                ->exists();
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}

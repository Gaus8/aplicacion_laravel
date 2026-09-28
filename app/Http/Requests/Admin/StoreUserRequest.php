<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasPermission('users.manage') ?? false; }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'], 'is_active' => ['required', 'boolean'],
            'roles' => ['array'], 'roles.*' => ['integer', 'distinct', 'exists:roles,id'],
        ];
    }
}

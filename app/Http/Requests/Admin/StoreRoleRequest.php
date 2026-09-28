<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasPermission('roles.manage') ?? false; }
    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:80', 'unique:roles,name'], 'description' => ['nullable', 'string', 'max:255'], 'permissions' => ['array'], 'permissions.*' => ['string', 'distinct', 'exists:permissions,name']];
    }
}

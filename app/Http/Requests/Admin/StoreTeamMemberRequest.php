<?php

namespace App\Http\Requests\Admin;

use App\Models\TeamMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreTeamMemberRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', TeamMember::class) ?? false; }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:140'],
            'role' => ['required', 'string', 'max:120'],
            'bio' => ['required', 'string', 'max:5000'],
            'email' => ['nullable', 'email', 'max:254'],
            'profile_url' => ['nullable', 'url:https', 'max:2048'],
            'image' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(5120)],
            'image_alt' => ['nullable', 'required_with:image', 'string', 'max:180'],
            'position' => ['required', 'integer', 'min:0', 'max:10000'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}

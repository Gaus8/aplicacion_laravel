<?php

namespace App\Http\Requests\Admin;

use App\Models\SocialLink;
use Illuminate\Foundation\Http\FormRequest;

class IndexSocialLinkRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('viewAny', SocialLink::class) ?? false; }
    public function rules(): array { return ['search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:active,inactive']]; }
}

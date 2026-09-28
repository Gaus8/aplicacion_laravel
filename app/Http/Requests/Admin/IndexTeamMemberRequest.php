<?php

namespace App\Http\Requests\Admin;

use App\Models\TeamMember;
use Illuminate\Foundation\Http\FormRequest;

class IndexTeamMemberRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('viewAny', TeamMember::class) ?? false; }
    public function rules(): array { return ['search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:active,inactive']]; }
}

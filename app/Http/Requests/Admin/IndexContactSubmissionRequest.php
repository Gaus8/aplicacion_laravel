<?php

namespace App\Http\Requests\Admin;

use App\Models\ContactSubmission;
use Illuminate\Foundation\Http\FormRequest;

class IndexContactSubmissionRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('viewAny', ContactSubmission::class) ?? false; }
    public function rules(): array { return ['search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:unread,read']]; }
}

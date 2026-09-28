<?php

namespace App\Http\Requests\Admin;

use App\Models\ContactSubmission;
use Illuminate\Foundation\Http\FormRequest;

class MarkContactReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $submission = $this->route('submission');

        return $submission instanceof ContactSubmission && ($this->user()?->can('update', $submission) ?? false);
    }
    public function rules(): array { return []; }
}

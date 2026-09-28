<?php

namespace App\Http\Requests\Admin;

use App\Models\Video;
use App\Support\VideoUrlParser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', Video::class) ?? false; }
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'video_url' => ['required', 'url:http,https', 'max:2048'],
            'position' => ['required', 'integer', 'min:0', 'max:10000'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
    public function after(): array
    {
        return [function (Validator $validator): void {
            if (is_string($this->input('video_url')) && $this->filled('video_url') && !app(VideoUrlParser::class)->parse($this->input('video_url'))) {
                $validator->errors()->add('video_url', 'Usa un enlace válido de YouTube o Vimeo.');
            }
        }];
    }
}

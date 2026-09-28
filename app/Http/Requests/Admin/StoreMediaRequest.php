<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Media::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'file' => ['required', File::image()->types(['jpg', 'jpeg', 'png', 'webp', 'gif'])->max(5120)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Indica un nombre para la imagen.',
            'file.required' => 'Selecciona una imagen para cargar.',
            'file.image' => 'El archivo debe ser una imagen válida.',
            'file.max' => 'La imagen no puede superar los 5 MB.',
        ];
    }
}

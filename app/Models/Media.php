<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'name',
        'path',
        'mime_type',
        'size',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'size' => 'integer',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AboutPage extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'body', 'mission', 'vision', 'image_path', 'image_alt', 'active', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

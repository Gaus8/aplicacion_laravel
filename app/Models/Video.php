<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    protected $fillable = ['title', 'description', 'provider', 'external_id', 'video_url', 'position', 'active', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'position' => 'integer'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getEmbedUrlAttribute(): string
    {
        return match ($this->provider) {
            'youtube' => 'https://www.youtube-nocookie.com/embed/'.rawurlencode($this->external_id),
            'vimeo' => 'https://player.vimeo.com/video/'.rawurlencode($this->external_id),
            default => '',
        };
    }
}

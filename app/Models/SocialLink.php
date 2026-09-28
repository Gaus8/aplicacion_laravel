<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialLink extends Model
{
    public const PLATFORMS = ['facebook', 'instagram', 'linkedin', 'tiktok', 'x', 'youtube', 'whatsapp', 'github'];

    protected $fillable = ['platform', 'label', 'url', 'position', 'active', 'created_by', 'updated_by'];

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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true)->orderBy('position')->orderBy('id');
    }
}

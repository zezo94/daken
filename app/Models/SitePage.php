<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SitePage extends Model
{
    protected $fillable = ['key', 'title', 'body', 'image', 'is_active'];

    protected function casts(): array
    {
        return ['title' => 'array', 'body' => 'array', 'is_active' => 'boolean'];
    }

    public function getTranslatedTitleAttribute(): string
    {
        return $this->title[app()->getLocale()] ?? $this->title['en'] ?? $this->title['ar'] ?? '';
    }

    public function getTranslatedBodyAttribute(): string
    {
        return $this->body[app()->getLocale()] ?? $this->body['en'] ?? $this->body['ar'] ?? '';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }
}

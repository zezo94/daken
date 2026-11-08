<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'slug',
        'sku',
        'weight',
        'image',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'weight' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getTranslatedNameAttribute(): string
    {
        return $this->name[app()->getLocale()]
            ?? $this->name['en']
            ?? '';
    }



    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true);
    }




    public function getDisplayImagePathAttribute(): ?string
    {
        $primary = $this->relationLoaded('primaryImage')
            ? $this->getRelation('primaryImage')
            : $this->primaryImage()->first();

        return $primary?->path
            ?? $this->image;
    }


    public function views(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ProductView::class);
    }


}

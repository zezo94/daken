<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Location extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'country',
        'phone',
        'latitude',
        'longitude',
        'google_maps_url',
        'image',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'address' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getTranslatedNameAttribute(): string
    {
        $name = $this->name ?? [];

        return $name[app()->getLocale()]
            ?? $name['ar']
            ?? $name['en']
            ?? '';
    }

    public function getTranslatedAddressAttribute(): string
    {
        $address = $this->address ?? [];

        return $address[app()->getLocale()]
            ?? $address['ar']
            ?? $address['en']
            ?? '';
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return Storage::disk('public')->url($this->image);
        }

        return asset('images/location-placeholder.svg');
    }

    public function getMapsUrlAttribute(): string
    {
        if ($this->google_maps_url) {
            return $this->google_maps_url;
        }

        if ($this->latitude !== null && $this->longitude !== null) {
            return 'https://www.google.com/maps/search/?api=1&query='
                . rawurlencode($this->latitude . ',' . $this->longitude);
        }

        $query = trim(implode(' ', array_filter([
            $this->translated_name,
            $this->translated_address,
            $this->city,
            $this->country,
        ])));

        return 'https://www.google.com/maps/search/?api=1&query='
            . rawurlencode($query);
    }
}

<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SiteSettings
{
    private const CACHE_KEY = 'daken.site_settings';

    public function all(): array
    {
        return Cache::remember(
            self::CACHE_KEY,
            now()->addHour(),
            fn () => SiteSetting::query()
                ->pluck('value', 'key')
                ->all()
        );
    }

    public function get(
        string $key,
        ?string $default = null
    ): ?string {
        return $this->all()[$key] ?? $default;
    }

    public function setMany(array $settings): void
    {
        DB::transaction(function () use ($settings) {
            foreach ($settings as $key => $value) {
                SiteSetting::query()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        });

        Cache::forget(self::CACHE_KEY);
    }
}

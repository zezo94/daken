<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationSetting extends Model
{
    protected $table = 'navigation_settings';

    protected $fillable = [
        'key',
        'placement',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}

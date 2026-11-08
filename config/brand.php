<?php


return [
    'name' => 'DAKEN',
    'full_name' => 'DAKEN Chocolate',

    'languages' => ['ar', 'en'],
    'default_language' => 'ar',

    'contact' => [
        'email' => env('DAKEN_CONTACT_EMAIL', ''),
        'phone' => env('DAKEN_CONTACT_PHONE', ''),
    ],

    'social' => [
        'instagram' => env('DAKEN_INSTAGRAM', ''),
        'facebook' => env('DAKEN_FACEBOOK', ''),
    ],
];

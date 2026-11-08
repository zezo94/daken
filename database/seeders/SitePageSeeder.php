<?php

namespace Database\Seeders;

use App\Models\SitePage;
use Illuminate\Database\Seeder;

class SitePageSeeder extends Seeder
{
    public function run(): void
    {
        SitePage::updateOrCreate(
            ['key' => 'about'],
            [
                'title' => [
                    'ar' => 'من نحن',
                    'en' => 'About Us',
                ],
                'body' => [
                    'ar' => 'تعرف على قصة DAKEN وشغفنا بالشوكولاتة.',
                    'en' => 'Discover the DAKEN story and our passion for chocolate.',
                ],
                'is_active' => true,
            ]
        );

        SitePage::updateOrCreate(
            ['key' => 'vision'],
            [
                'title' => [
                    'ar' => 'رؤيتنا',
                    'en' => 'Our Vision',
                ],
                'body' => [
                    'ar' => 'نسعى إلى تقديم تجربة شوكولاتة مميزة.',
                    'en' => 'We aspire to deliver a distinctive chocolate experience.',
                ],
                'is_active' => true,
            ]
        );
    }
}

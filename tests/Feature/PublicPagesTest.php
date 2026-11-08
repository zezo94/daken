<?php

namespace Tests\Feature;

use App\Models\SitePage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_accessible(): void
    {
        $this->get('/')
            ->assertOk();
    }

    public function test_products_page_is_accessible(): void
    {
        $this->get('/products')
            ->assertOk();
    }

    public function test_locations_page_is_accessible(): void
    {
        $this->get('/locations')
            ->assertOk();
    }

    public function test_contact_page_is_accessible(): void
    {
        $this->get('/contact')
            ->assertOk();
    }

    public function test_about_page_is_accessible_when_active(): void
    {
        $page = new SitePage();

        $page->forceFill([
            'key' => 'about',
            'title' => [
                'ar' => 'من نحن',
                'en' => 'About Us',
            ],
            'body' => [
                'ar' => 'محتوى الصفحة',
                'en' => 'Page content',
            ],
            'is_active' => true,
        ]);

        $page->save();

        $this->get('/about')
            ->assertOk();
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicRegistrationDisabledTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_routes_are_disabled(): void
    {
        $this->assertFalse(Route::has('register'));
        $this->assertFalse(Route::has('register.store'));
    }

    public function test_guest_cannot_open_registration_page(): void
    {
        $this->get('/register')
            ->assertNotFound();
    }

    public function test_guest_cannot_submit_registration_form(): void
    {
        $this->post('/register', [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', [
            'email' => 'visitor@example.com',
        ]);
    }

    public function test_public_homepage_does_not_show_login_or_register_links(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('href="' . route('login') . '"', false)
            ->assertDontSee('href="/register"', false);
    }

    public function test_administrator_login_page_remains_available(): void
    {
        $this->get('/login')
            ->assertOk();
    }
}

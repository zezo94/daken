<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get('/admin')
            ->assertRedirect();
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->get('/admin')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = \App\Models\User::factory()->create();

        $admin->forceFill([
            'role' => 'admin',
            'is_super_admin' => true,
            'admin_level' => 100,
            'admin_permissions' => [],
            'is_active' => true,
        ])->save();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }
}

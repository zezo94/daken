<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTwoFactorInactiveSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_admin_cannot_complete_two_factor_challenge(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Simulate a pending Fortify 2FA challenge.
        $admin->is_active = false;
        $admin->save();

        $admin->refresh();

        $response = $this
            ->withSession([
                'login.id' => $admin->id,
                'login.remember' => false,
            ])
            ->post(route('two-factor.login.store'), [
                'code' => '123456',
            ]);

        $response->assertRedirect(route('login'));

        $response->assertSessionMissing('login.id');
        $response->assertSessionMissing('login.remember');

        $this->assertGuest();
    }

    public function test_active_admin_is_not_rejected_by_inactive_admin_middleware(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this
            ->withSession([
                'login.id' => $admin->id,
            ])
            ->post(route('two-factor.login.store'), [
                'code' => 'invalid-code',
            ]);

        // Invalid 2FA code must fail normally,
        // rather than being rejected as an inactive admin.
        $response->assertSessionHas('login.id');

        $this->assertGuest();
    }

    public function test_customer_cannot_complete_admin_two_factor_challenge(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => false,
        ]);

        $response = $this
            ->withSession([
                'login.id' => $customer->id,
                'login.remember' => false,
            ])
            ->post(route('two-factor.login.store'), [
                'code' => 'invalid-code',
            ]);

        $response->assertRedirect(route('login'));

        $response->assertSessionHasErrors('email');

        $response->assertSessionMissing('login.id');

        $response->assertSessionMissing('login.remember');

        $this->assertGuest('web');
    }


    public function test_inactive_admin_policy_and_database_state(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $admin->is_active = false;
        $admin->save();

        $admin->refresh();

        $admin->refresh();

        $this->assertSame('admin', $admin->role);
        $this->assertFalse($admin->is_active);

        $this->assertFalse(
            \App\Support\AdminLoginPolicy::allows($admin)
        );
    }

}

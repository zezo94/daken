<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminInactiveLoginSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(bool $active): User
    {
        $user = User::factory()->create([
            'password' => Hash::make('SecurePassword123!'),
        ]);

        $user->forceFill([
            'role' => 'admin',
            'is_super_admin' => false,
            'admin_level' => 20,
            'admin_permissions' => ['products.manage'],
            'is_active' => $active,
        ])->save();

        return $user;
    }

    public function test_active_admin_can_login(): void
    {
        $admin = $this->makeAdmin(true);

        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'SecurePassword123!',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($admin);
    }

    public function test_inactive_admin_cannot_login(): void
    {
        $admin = $this->makeAdmin(false);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $admin->email,
                'password' => 'SecurePassword123!',
            ])
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_inactive_admin_cannot_access_dashboard(): void
    {
        $admin = $this->makeAdmin(false);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }}

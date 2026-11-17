<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(
        array $permissions = [],
        bool $active = true,
        bool $superAdmin = false
    ): User {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'admin',
            'is_super_admin' => $superAdmin,
            'admin_level' => $superAdmin ? 100 : 20,
            'admin_permissions' => $permissions,
            'is_active' => $active,
        ])->save();

        return $user->fresh();
    }

    public function test_inactive_admin_cannot_access_dashboard(): void
    {
        $admin = $this->makeAdmin(
            active: false
        );

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }

    public function test_inactive_admin_cannot_access_activity_log(): void
    {
        $admin = $this->makeAdmin(
            permissions: ['activities.view'],
            active: false
        );

        $this->actingAs($admin)
            ->get(route('admin.activities.index'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }

    public function test_inactive_admin_cannot_export_activities(): void
    {
        $admin = $this->makeAdmin(
            permissions: ['activities.view'],
            active: false
        );

        $this->actingAs($admin)
            ->get(route('admin.activities.export'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }

    public function test_admin_without_activity_permission_cannot_view_log(): void
    {
        $admin = $this->makeAdmin(
            permissions: ['products.manage']
        );

        $this->actingAs($admin)
            ->get(route('admin.activities.index'))
            ->assertForbidden();
    }

    public function test_admin_without_activity_permission_cannot_export(): void
    {
        $admin = $this->makeAdmin(
            permissions: ['products.manage']
        );

        $this->actingAs($admin)
            ->get(route('admin.activities.export'))
            ->assertForbidden();
    }

    public function test_admin_without_activity_permission_cannot_view_details(): void
    {
        $admin = $this->makeAdmin(
            permissions: ['products.manage']
        );

        $activity = AdminActivity::query()->create([
            'user_id' => $admin->id,
            'action' => 'admin.created',
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'details' => [
                'admin_level' => 20,
                'admin_permissions' => [],
                'is_active' => true,
            ],
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.activities.show', $activity))
            ->assertForbidden();
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->create();

        $customer->forceFill([
            'role' => 'customer',
            'is_super_admin' => false,
            'admin_level' => 0,
            'admin_permissions' => [],
            'is_active' => true,
        ])->save();

        $this->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }

    public function test_super_admin_can_access_activity_log(): void
    {
        $admin = $this->makeAdmin(
            superAdmin: true
        );

        $this->actingAs($admin)
            ->get(route('admin.activities.index'))
            ->assertOk();
    }

    public function test_removed_permission_blocks_next_request(): void
    {
        $admin = $this->makeAdmin(
            permissions: ['activities.view']
        );

        $this->actingAs($admin)
            ->get(route('admin.activities.index'))
            ->assertOk();

        $admin->forceFill([
            'admin_permissions' => [],
        ])->save();

        $this->actingAs($admin->fresh())
            ->get(route('admin.activities.index'))
            ->assertForbidden();
    }

    public function test_deactivated_admin_is_blocked_on_next_request(): void
    {
        $admin = $this->makeAdmin(
            permissions: ['activities.view']
        );

        $this->actingAs($admin)
            ->get(route('admin.activities.index'))
            ->assertOk();

        $admin->forceFill([
            'is_active' => false,
        ])->save();

        $this->actingAs($admin->fresh())
            ->get(route('admin.activities.index'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }
}

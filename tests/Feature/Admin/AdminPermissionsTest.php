<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(
        array $permissions = [],
        int $level = 20,
        bool $superAdmin = false,
        bool $active = true
    ): User {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'admin',
            'is_super_admin' => $superAdmin,
            'admin_level' => $superAdmin ? 100 : $level,
            'admin_permissions' => $permissions,
            'is_active' => $active,
        ])->save();

        return $user->fresh();
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $admin = $this->makeAdmin(
            superAdmin: true
        );

        foreach (array_keys(User::PERMISSIONS) as $permission) {
            $this->assertTrue(
                $admin->hasAdminPermission($permission)
            );
        }
    }

    public function test_admin_only_has_assigned_permissions(): void
    {
        $admin = $this->makeAdmin([
            'products.manage',
        ]);

        $this->assertTrue(
            $admin->hasAdminPermission('products.manage')
        );

        $this->assertFalse(
            $admin->hasAdminPermission('users.manage')
        );

        $this->assertFalse(
            $admin->hasAdminPermission('analytics.view')
        );
    }

    public function test_inactive_admin_has_no_permissions(): void
    {
        $admin = $this->makeAdmin(
            permissions: ['products.manage'],
            active: false
        );

        $this->assertFalse(
            $admin->hasAdminPermission('products.manage')
        );
    }

    public function test_admin_cannot_manage_higher_level_user(): void
    {
        $manager = $this->makeAdmin(
            permissions: ['users.manage'],
            level: 50
        );

        $higherManager = $this->makeAdmin(
            level: 70
        );

        $this->assertFalse(
            $manager->canManageUser($higherManager)
        );
    }

    public function test_admin_can_manage_lower_level_user(): void
    {
        $manager = $this->makeAdmin(
            permissions: ['users.manage'],
            level: 70
        );

        $employee = $this->makeAdmin(
            level: 20
        );

        $this->assertTrue(
            $manager->canManageUser($employee)
        );
    }

    public function test_admin_cannot_manage_self(): void
    {
        $admin = $this->makeAdmin(
            permissions: ['users.manage'],
            level: 70
        );

        $this->assertFalse(
            $admin->canManageUser($admin)
        );
    }

    public function test_super_admin_cannot_be_managed(): void
    {
        $superAdmin = $this->makeAdmin(
            superAdmin: true
        );

        $manager = $this->makeAdmin(
            permissions: ['users.manage'],
            level: 70
        );

        $this->assertFalse(
            $manager->canManageUser($superAdmin)
        );
    }

    public function test_admin_cannot_assign_higher_level(): void
    {
        $manager = $this->makeAdmin(
            permissions: ['users.manage'],
            level: 50
        );

        $this->assertTrue(
            $manager->canAssignLevel(20)
        );

        $this->assertFalse(
            $manager->canAssignLevel(50)
        );

        $this->assertFalse(
            $manager->canAssignLevel(70)
        );
    }

    public function test_admin_cannot_grant_unowned_permissions(): void
    {
        $manager = $this->makeAdmin(
            permissions: [
                'users.manage',
                'products.manage',
            ],
            level: 70
        );

        $this->assertTrue(
            $manager->canAssignPermissions([
                'products.manage',
            ])
        );

        $this->assertFalse(
            $manager->canAssignPermissions([
                'settings.manage',
            ])
        );
    }

    public function test_user_without_permission_cannot_open_products(): void
    {
        $admin = $this->makeAdmin([
            'locations.manage',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_user_without_permission_cannot_open_user_management(): void
    {
        $admin = $this->makeAdmin([
            'products.manage',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_inactive_admin_cannot_open_dashboard(): void
    {
        $admin = $this->makeAdmin(
            permissions: ['products.manage'],
            active: false
        );

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }


    public function test_admin_cannot_manage_customer_account(): void
    {
        $manager = $this->makeAdmin(
            permissions: ['users.manage'],
            level: 70
        );

        $customer = User::factory()->create();

        $customer->forceFill([
            'role' => 'customer',
            'is_super_admin' => false,
            'admin_level' => 0,
        ])->save();

        $this->assertFalse(
            $manager->canManageUser($customer)
        );

        $this->actingAs($manager)
            ->get(route('admin.users.edit', $customer))
            ->assertForbidden();

        $this->actingAs($manager)
            ->delete(route('admin.users.destroy', $customer))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'role' => 'customer',
        ]);
    }

}

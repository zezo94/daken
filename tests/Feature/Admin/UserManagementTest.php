<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(
        int $level = 70,
        array $permissions = ['users.manage'],
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

    private function validData(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Test Employee',
            'email' => 'employee@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'admin_level' => 20,
            'admin_permissions' => ['products.manage'],
            'is_active' => 1,
        ], $overrides);
    }

    public function test_super_admin_can_create_admin(): void
    {
        $actor = $this->makeAdmin(
            superAdmin: true
        );

        $this->actingAs($actor)
            ->post(
                route('admin.users.store'),
                $this->validData()
            )
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'employee@example.com',
            'role' => 'admin',
            'admin_level' => 20,
            'is_active' => 1,
            'is_super_admin' => 0,
        ]);

        $created = User::query()
            ->where('email', 'employee@example.com')
            ->firstOrFail();

        $this->assertSame(
            ['products.manage'],
            $created->admin_permissions
        );
    }

    public function test_manager_can_create_lower_level_admin(): void
    {
        $actor = $this->makeAdmin(
            level: 70,
            permissions: [
                'users.manage',
                'products.manage',
            ]
        );

        $this->actingAs($actor)
            ->post(
                route('admin.users.store'),
                $this->validData()
            )
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'employee@example.com',
            'admin_level' => 20,
        ]);
    }

    public function test_manager_cannot_create_equal_level_admin(): void
    {
        $actor = $this->makeAdmin(
            level: 70,
            permissions: [
                'users.manage',
                'products.manage',
            ]
        );

        $this->actingAs($actor)
            ->post(
                route('admin.users.store'),
                $this->validData([
                    'admin_level' => 70,
                ])
            )
            ->assertSessionHasErrors('admin_level');

        $this->assertDatabaseMissing('users', [
            'email' => 'employee@example.com',
        ]);
    }

    public function test_manager_cannot_grant_unowned_permission(): void
    {
        $actor = $this->makeAdmin(
            level: 70,
            permissions: ['users.manage']
        );

        $this->actingAs($actor)
            ->post(
                route('admin.users.store'),
                $this->validData()
            )
            ->assertSessionHasErrors('admin_permissions');

        $this->assertDatabaseMissing('users', [
            'email' => 'employee@example.com',
        ]);
    }

    public function test_admin_without_users_permission_cannot_create_admin(): void
    {
        $actor = $this->makeAdmin(
            permissions: ['products.manage']
        );

        $this->actingAs($actor)
            ->post(
                route('admin.users.store'),
                $this->validData()
            )
            ->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => 'employee@example.com',
        ]);
    }

    public function test_manager_can_update_lower_level_admin(): void
    {
        $actor = $this->makeAdmin(
            level: 70,
            permissions: [
                'users.manage',
                'products.manage',
            ]
        );

        $target = $this->makeAdmin(
            level: 20,
            permissions: []
        );

        $this->actingAs($actor)
            ->put(
                route('admin.users.update', $target),
                $this->validData([
                    'name' => 'Updated Employee',
                    'email' => $target->email,
                    'password' => '',
                    'password_confirmation' => '',
                ])
            )
            ->assertRedirect(route('admin.users.index'));

        $target->refresh();

        $this->assertSame(
            'Updated Employee',
            $target->name
        );

        $this->assertSame(
            ['products.manage'],
            $target->admin_permissions
        );
    }

    public function test_manager_cannot_promote_employee_to_equal_level(): void
    {
        $actor = $this->makeAdmin(
            level: 70,
            permissions: [
                'users.manage',
                'products.manage',
            ]
        );

        $target = $this->makeAdmin(
            level: 20
        );

        $this->actingAs($actor)
            ->put(
                route('admin.users.update', $target),
                $this->validData([
                    'email' => $target->email,
                    'password' => '',
                    'password_confirmation' => '',
                    'admin_level' => 70,
                ])
            )
            ->assertSessionHasErrors('admin_level');

        $this->assertSame(
            20,
            $target->fresh()->admin_level
        );
    }

    public function test_manager_cannot_update_self(): void
    {
        $actor = $this->makeAdmin(
            level: 70
        );

        $this->actingAs($actor)
            ->put(
                route('admin.users.update', $actor),
                $this->validData([
                    'email' => $actor->email,
                    'password' => '',
                    'password_confirmation' => '',
                ])
            )
            ->assertForbidden();
    }

    public function test_manager_cannot_update_super_admin(): void
    {
        $actor = $this->makeAdmin(
            level: 70
        );

        $superAdmin = $this->makeAdmin(
            superAdmin: true
        );

        $this->actingAs($actor)
            ->put(
                route('admin.users.update', $superAdmin),
                $this->validData([
                    'email' => $superAdmin->email,
                    'password' => '',
                    'password_confirmation' => '',
                ])
            )
            ->assertForbidden();
    }

    public function test_manager_can_deactivate_lower_level_admin(): void
    {
        $actor = $this->makeAdmin(
            level: 70
        );

        $target = $this->makeAdmin(
            level: 20
        );

        $this->actingAs($actor)
            ->delete(
                route('admin.users.destroy', $target)
            )
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'role' => 'admin',
            'is_active' => 0,
        ]);

        $this->assertFalse(
            $target->fresh()->is_active
        );
    }

    public function test_deactivated_admin_cannot_access_dashboard(): void
    {
        $actor = $this->makeAdmin(
            level: 70
        );

        $target = $this->makeAdmin(
            level: 20,
            permissions: ['products.manage']
        );

        $this->actingAs($actor)
            ->delete(
                route('admin.users.destroy', $target)
            )
            ->assertRedirect(route('admin.users.index'));

        $this->actingAs($target->fresh())
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_manager_cannot_deactivate_super_admin(): void
    {
        $actor = $this->makeAdmin(
            level: 70
        );

        $target = $this->makeAdmin(
            superAdmin: true
        );

        $this->actingAs($actor)
            ->delete(
                route('admin.users.destroy', $target)
            )
            ->assertForbidden();

        $this->assertTrue(
            $target->fresh()->is_active
        );
    }

    public function test_manager_cannot_deactivate_self(): void
    {
        $actor = $this->makeAdmin(
            level: 70
        );

        $this->actingAs($actor)
            ->delete(
                route('admin.users.destroy', $actor)
            )
            ->assertForbidden();

        $this->assertTrue(
            $actor->fresh()->is_active
        );
    }

    public function test_manager_cannot_deactivate_customer(): void
    {
        $actor = $this->makeAdmin(
            level: 70
        );

        $customer = User::factory()->create();

        $customer->forceFill([
            'role' => 'customer',
            'is_active' => true,
        ])->save();

        $this->actingAs($actor)
            ->delete(
                route('admin.users.destroy', $customer)
            )
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'role' => 'customer',
            'is_active' => 1,
        ]);
    }

    public function test_deactivated_admin_remains_in_database(): void
    {
        $actor = $this->makeAdmin(
            level: 70
        );

        $target = $this->makeAdmin(
            level: 20
        );

        $targetId = $target->id;

        $this->actingAs($actor)
            ->delete(
                route('admin.users.destroy', $target)
            )
            ->assertRedirect(route('admin.users.index'));

        $this->assertNotNull(
            User::query()->find($targetId)
        );
    }
}

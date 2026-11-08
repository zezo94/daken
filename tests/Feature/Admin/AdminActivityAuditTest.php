<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActivityAuditTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(
        int $level = 70,
        array $permissions = ['users.manage'],
        bool $superAdmin = false
    ): User {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'admin',
            'is_super_admin' => $superAdmin,
            'admin_level' => $superAdmin ? 100 : $level,
            'admin_permissions' => $permissions,
            'is_active' => true,
        ])->save();

        return $user->fresh();
    }

    private function validData(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Audit Employee',
            'email' => 'audit-employee@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'admin_level' => 20,
            'admin_permissions' => ['products.manage'],
            'is_active' => 1,
        ], $overrides);
    }

    public function test_creating_admin_records_activity(): void
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

        $target = User::query()
            ->where('email', 'audit-employee@example.com')
            ->firstOrFail();

        $activity = AdminActivity::query()
            ->where('action', 'admin.created')
            ->firstOrFail();

        $this->assertSame($actor->id, $activity->user_id);
        $this->assertSame(User::class, $activity->subject_type);
        $this->assertSame($target->id, $activity->subject_id);

        $this->assertSame(
            20,
            $activity->details['admin_level']
        );

        $this->assertSame(
            ['products.manage'],
            $activity->details['admin_permissions']
        );
    }

    public function test_updating_admin_records_before_and_after(): void
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
                    'email' => $target->email,
                    'password' => '',
                    'password_confirmation' => '',
                    'admin_level' => 30,
                ])
            )
            ->assertRedirect(route('admin.users.index'));

        $activity = AdminActivity::query()
            ->where('action', 'admin.updated')
            ->firstOrFail();

        $this->assertSame($actor->id, $activity->user_id);
        $this->assertSame($target->id, $activity->subject_id);

        $this->assertSame(
            20,
            $activity->details['before']['admin_level']
        );

        $this->assertSame(
            30,
            $activity->details['after']['admin_level']
        );

        $this->assertSame(
            [],
            $activity->details['before']['admin_permissions']
        );

        $this->assertSame(
            ['products.manage'],
            $activity->details['after']['admin_permissions']
        );
    }

    public function test_deactivating_admin_records_activity(): void
    {
        $actor = $this->makeAdmin();

        $target = $this->makeAdmin(
            level: 20
        );

        $this->actingAs($actor)
            ->delete(
                route('admin.users.destroy', $target)
            )
            ->assertRedirect(route('admin.users.index'));

        $activity = AdminActivity::query()
            ->where('action', 'admin.deactivated')
            ->firstOrFail();

        $this->assertSame($actor->id, $activity->user_id);
        $this->assertSame($target->id, $activity->subject_id);

        $this->assertTrue(
            $activity->details['was_active']
        );

        $this->assertFalse(
            $activity->details['is_active']
        );

        $this->assertFalse(
            $target->fresh()->is_active
        );
    }

    public function test_rejected_creation_does_not_record_success(): void
    {
        $actor = $this->makeAdmin(
            level: 70,
            permissions: ['users.manage']
        );

        $this->actingAs($actor)
            ->post(
                route('admin.users.store'),
                $this->validData([
                    'admin_permissions' => [
                        'settings.manage',
                    ],
                ])
            )
            ->assertSessionHasErrors('admin_permissions');

        $this->assertSame(
            0,
            AdminActivity::query()
                ->where('action', 'admin.created')
                ->count()
        );

        $this->assertDatabaseMissing('users', [
            'email' => 'audit-employee@example.com',
        ]);
    }

    public function test_rejected_update_does_not_record_success(): void
    {
        $actor = $this->makeAdmin(
            level: 70
        );

        $target = $this->makeAdmin(
            level: 70
        );

        $this->actingAs($actor)
            ->put(
                route('admin.users.update', $target),
                $this->validData([
                    'email' => $target->email,
                    'password' => '',
                    'password_confirmation' => '',
                    'admin_permissions' => [],
                ])
            )
            ->assertForbidden();

        $this->assertSame(
            0,
            AdminActivity::query()
                ->where('action', 'admin.updated')
                ->count()
        );
    }

    public function test_rejected_deactivation_does_not_record_success(): void
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

        $this->assertSame(
            0,
            AdminActivity::query()
                ->where('action', 'admin.deactivated')
                ->count()
        );

        $this->assertTrue(
            $target->fresh()->is_active
        );
    }

    public function test_password_is_not_stored_in_activity_details(): void
    {
        $actor = $this->makeAdmin(
            superAdmin: true
        );

        $password = 'SecurePassword123!';

        $this->actingAs($actor)
            ->post(
                route('admin.users.store'),
                $this->validData([
                    'password' => $password,
                    'password_confirmation' => $password,
                ])
            )
            ->assertRedirect(route('admin.users.index'));

        $activity = AdminActivity::query()
            ->where('action', 'admin.created')
            ->firstOrFail();

        $details = json_encode(
            $activity->details,
            JSON_THROW_ON_ERROR
        );

        $this->assertStringNotContainsString(
            $password,
            $details
        );

        $this->assertArrayNotHasKey(
            'password',
            $activity->details
        );

        $this->assertArrayNotHasKey(
            'password_confirmation',
            $activity->details
        );
    }

    public function test_password_change_records_flag_not_secret(): void
    {
        $actor = $this->makeAdmin(
            superAdmin: true
        );

        $target = $this->makeAdmin(
            level: 20
        );

        $newPassword = 'NewSecurePassword456!';

        $this->actingAs($actor)
            ->put(
                route('admin.users.update', $target),
                $this->validData([
                    'email' => $target->email,
                    'password' => $newPassword,
                    'password_confirmation' => $newPassword,
                ])
            )
            ->assertRedirect(route('admin.users.index'));

        $activity = AdminActivity::query()
            ->where('action', 'admin.updated')
            ->firstOrFail();

        $this->assertTrue(
            $activity->details['password_changed']
        );

        $this->assertStringNotContainsString(
            $newPassword,
            json_encode(
                $activity->details,
                JSON_THROW_ON_ERROR
            )
        );
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPasswordSessionRevocationTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(
        int $level,
        array $permissions
    ): User {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'admin',
            'is_super_admin' => false,
            'admin_level' => $level,
            'admin_permissions' => $permissions,
            'is_active' => true,
        ])->save();

        return $user->fresh();
    }

    private function createSession(
        User $user,
        string $sessionId
    ): void {
        DB::table('sessions')->insert([
            'id' => $sessionId,
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'DAKEN Password Test',
            'payload' => base64_encode('test'),
            'last_activity' => time(),
        ]);
    }

    private function updateData(
        User $target,
        array $overrides = []
    ): array {
        return array_merge([
            'name' => $target->name,
            'email' => $target->email,
            'admin_level' => 20,
            'admin_permissions' => ['products.manage'],
            'is_active' => '1',
        ], $overrides);
    }

    public function test_password_change_revokes_target_sessions(): void
    {
        $actor = $this->makeAdmin(
            70,
            ['users.manage', 'products.manage']
        );

        $target = $this->makeAdmin(
            20,
            ['products.manage']
        );

        $this->createSession($actor, 'actor-password-session');
        $this->createSession($target, 'target-password-session');

        $newPassword = 'DAKEN-Secure-Password-2026!';

        $this->actingAs($actor)
            ->put(
                route('admin.users.update', $target),
                $this->updateData($target, [
                    'password' => $newPassword,
                    'password_confirmation' => $newPassword,
                ])
            )
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue(
            Hash::check(
                $newPassword,
                $target->fresh()->password
            )
        );

        $this->assertDatabaseMissing('sessions', [
            'id' => 'target-password-session',
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => 'actor-password-session',
            'user_id' => $actor->id,
        ]);

        $this->assertDatabaseHas('admin_activities', [
            'user_id' => $actor->id,
            'action' => 'admin.updated',
            'subject_id' => $target->id,
        ]);

        $activity = AdminActivity::query()
            ->where('action', 'admin.updated')
            ->where('subject_id', $target->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertTrue(
            $activity->details['password_changed']
        );

        $this->assertStringNotContainsString(
            $newPassword,
            json_encode($activity->details)
        );
    }

    public function test_regular_update_preserves_target_sessions(): void
    {
        $actor = $this->makeAdmin(
            70,
            ['users.manage', 'products.manage']
        );

        $target = $this->makeAdmin(
            20,
            ['products.manage']
        );

        $this->createSession($target, 'regular-update-session');

        $this->actingAs($actor)
            ->put(
                route('admin.users.update', $target),
                $this->updateData($target, [
                    'name' => 'Updated Administrator',
                ])
            )
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('sessions', [
            'id' => 'regular-update-session',
            'user_id' => $target->id,
        ]);
    }

    public function test_invalid_password_confirmation_preserves_sessions(): void
    {
        $actor = $this->makeAdmin(
            70,
            ['users.manage', 'products.manage']
        );

        $target = $this->makeAdmin(
            20,
            ['products.manage']
        );

        $this->createSession($target, 'invalid-password-session');

        $this->actingAs($actor)
            ->put(
                route('admin.users.update', $target),
                $this->updateData($target, [
                    'password' => 'DAKEN-Secure-Password-2026!',
                    'password_confirmation' => 'Wrong-Confirmation-2026!',
                ])
            )
            ->assertSessionHasErrors('password');

        $this->assertDatabaseHas('sessions', [
            'id' => 'invalid-password-session',
            'user_id' => $target->id,
        ]);
    }

    public function test_unauthorized_password_change_preserves_sessions(): void
    {
        $actor = $this->makeAdmin(
            20,
            ['users.manage', 'products.manage']
        );

        $target = $this->makeAdmin(
            70,
            ['products.manage']
        );

        $this->createSession($target, 'protected-password-session');

        $newPassword = 'DAKEN-Secure-Password-2026!';

        $this->actingAs($actor)
            ->put(
                route('admin.users.update', $target),
                $this->updateData($target, [
                    'password' => $newPassword,
                    'password_confirmation' => $newPassword,
                ])
            )
            ->assertForbidden();

        $this->assertDatabaseHas('sessions', [
            'id' => 'protected-password-session',
            'user_id' => $target->id,
        ]);

        $this->assertFalse(
            Hash::check(
                $newPassword,
                $target->fresh()->password
            )
        );
    }
}

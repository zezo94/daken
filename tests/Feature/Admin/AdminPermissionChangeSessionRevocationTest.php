<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminPermissionChangeSessionRevocationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(
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

    private function createSession(User $user, string $id): void
    {
        DB::table('sessions')->insert([
            'id' => $id,
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'DAKEN Permission Test',
            'payload' => base64_encode('test'),
            'last_activity' => time(),
        ]);
    }

    private function update(
        User $actor,
        User $target,
        int $level,
        array $permissions,
        ?string $name = null
    ): void {
        $this->actingAs($actor)
            ->put(route('admin.users.update', $target), [
                'name' => $name ?? $target->name,
                'email' => $target->email,
                'admin_level' => $level,
                'admin_permissions' => $permissions,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));
    }

    public function test_removing_permission_revokes_sessions(): void
    {
        $actor = $this->admin(70, [
            'users.manage',
            'products.manage',
            'categories.manage',
        ]);

        $target = $this->admin(20, [
            'products.manage',
            'categories.manage',
        ]);

        $this->createSession($target, 'removed-permission-session');

        $this->update(
            $actor,
            $target,
            20,
            ['products.manage']
        );

        $this->assertDatabaseMissing('sessions', [
            'id' => 'removed-permission-session',
        ]);
    }

    public function test_adding_permission_revokes_sessions(): void
    {
        $actor = $this->admin(70, [
            'users.manage',
            'products.manage',
            'categories.manage',
        ]);

        $target = $this->admin(20, [
            'products.manage',
        ]);

        $this->createSession($target, 'added-permission-session');

        $this->update(
            $actor,
            $target,
            20,
            ['products.manage', 'categories.manage']
        );

        $this->assertDatabaseMissing('sessions', [
            'id' => 'added-permission-session',
        ]);
    }

    public function test_changing_admin_level_revokes_sessions(): void
    {
        $actor = $this->admin(70, [
            'users.manage',
            'products.manage',
        ]);

        $target = $this->admin(50, [
            'products.manage',
        ]);

        $this->createSession($target, 'level-change-session');

        $this->update(
            $actor,
            $target,
            20,
            ['products.manage']
        );

        $this->assertDatabaseMissing('sessions', [
            'id' => 'level-change-session',
        ]);

        $this->assertSame(
            20,
            (int) $target->fresh()->admin_level
        );
    }

    public function test_name_change_preserves_sessions(): void
    {
        $actor = $this->admin(70, [
            'users.manage',
            'products.manage',
        ]);

        $target = $this->admin(20, [
            'products.manage',
        ]);

        $this->createSession($target, 'name-change-session');

        $this->update(
            $actor,
            $target,
            20,
            ['products.manage'],
            'Updated DAKEN Admin'
        );

        $this->assertDatabaseHas('sessions', [
            'id' => 'name-change-session',
            'user_id' => $target->id,
        ]);
    }

    public function test_permission_order_change_preserves_sessions(): void
    {
        $actor = $this->admin(70, [
            'users.manage',
            'products.manage',
            'categories.manage',
        ]);

        $target = $this->admin(20, [
            'products.manage',
            'categories.manage',
        ]);

        $this->createSession($target, 'permission-order-session');

        $this->update(
            $actor,
            $target,
            20,
            ['categories.manage', 'products.manage']
        );

        $this->assertDatabaseHas('sessions', [
            'id' => 'permission-order-session',
            'user_id' => $target->id,
        ]);
    }

    public function test_update_creates_exactly_one_activity(): void
    {
        $actor = $this->admin(70, [
            'users.manage',
            'products.manage',
        ]);

        $target = $this->admin(20, [
            'products.manage',
        ]);

        $beforeCount = AdminActivity::query()
            ->where('action', 'admin.updated')
            ->where('subject_id', $target->id)
            ->count();

        $this->update(
            $actor,
            $target,
            20,
            ['products.manage'],
            'DAKEN Updated Admin'
        );

        $afterCount = AdminActivity::query()
            ->where('action', 'admin.updated')
            ->where('subject_id', $target->id)
            ->count();

        $this->assertSame(
            1,
            $afterCount - $beforeCount
        );
    }

    public function test_permission_change_preserves_actor_session(): void
    {
        $actor = $this->admin(70, [
            'users.manage',
            'products.manage',
            'categories.manage',
        ]);

        $target = $this->admin(20, [
            'products.manage',
        ]);

        $this->createSession($actor, 'actor-session');
        $this->createSession($target, 'target-session');

        $this->update(
            $actor,
            $target,
            20,
            ['products.manage', 'categories.manage']
        );

        $this->assertDatabaseMissing('sessions', [
            'id' => 'target-session',
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => 'actor-session',
            'user_id' => $actor->id,
        ]);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminReactivationSessionSecurityTest extends TestCase
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
            'user_agent' => 'DAKEN Reactivation Security Test',
            'payload' => base64_encode('test'),
            'last_activity' => time(),
        ]);
    }

    public function test_reactivation_does_not_restore_revoked_session(): void
    {
        $actor = $this->makeAdmin(70, [
            'users.manage',
            'products.manage',
        ]);

        $target = $this->makeAdmin(20, [
            'products.manage',
        ]);

        $oldSessionId = Str::random(40);

        $this->createSession($target, $oldSessionId);

        // Deactivate using the real admin endpoint.
        $this->actingAs($actor)
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($target->fresh()->is_active);

        $this->assertDatabaseMissing('sessions', [
            'id' => $oldSessionId,
        ]);

        // Reactivate using the real admin update endpoint.
        $this->actingAs($actor)
            ->put(route('admin.users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'password' => '',
                'password_confirmation' => '',
                'admin_level' => 20,
                'admin_permissions' => ['products.manage'],
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue($target->fresh()->is_active);

        // Reactivation must never recreate an old session.
        $this->assertDatabaseMissing('sessions', [
            'id' => $oldSessionId,
        ]);
    }
}

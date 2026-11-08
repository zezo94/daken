<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Services\AdminSessionRevoker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminRevokedSessionReuseTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'admin',
            'is_super_admin' => false,
            'admin_level' => 20,
            'admin_permissions' => ['products.manage'],
            'is_active' => true,
        ])->save();

        return $user->fresh();
    }

    private function createAuthenticatedSession(
        User $admin,
        string $sessionId
    ): void {
        // Laravel's web guard stores the authenticated user ID
        // under a guard-specific session key.
        $guard = auth()->guard('web');

        $reflection = new \ReflectionMethod(
            $guard,
            'getName'
        );

        $loginKey = $reflection->invoke($guard);

        $payload = [
            '_token' => Str::random(40),
            $loginKey => $admin->getAuthIdentifier(),
        ];

        DB::table('sessions')->insert([
            'id' => $sessionId,
            'user_id' => $admin->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'DAKEN Session Reuse Test',
            'payload' => base64_encode(serialize($payload)),
            'last_activity' => time(),
        ]);
    }

    public function test_revoking_session_deletes_authenticated_session_record(): void
    {
        $admin = $this->makeAdmin();

        $sessionId = Str::random(40);

        $this->createAuthenticatedSession(
            $admin,
            $sessionId
        );

        $this->assertDatabaseHas('sessions', [
            'id' => $sessionId,
            'user_id' => $admin->id,
        ]);

        $deleted = app(AdminSessionRevoker::class)
            ->revokeForUser($admin->id);

        $this->assertSame(1, $deleted);

        $this->assertDatabaseMissing('sessions', [
            'id' => $sessionId,
        ]);
    }

    public function test_password_change_and_revocation_keep_account_active(): void
    {
        $admin = $this->makeAdmin();

        $sessionId = Str::random(40);

        $this->createAuthenticatedSession(
            $admin,
            $sessionId
        );

        $newPassword = 'DAKEN-New-Password-2026!';

        DB::transaction(function () use (
            $admin,
            $newPassword
        ): void {
            $admin->forceFill([
                'password' => Hash::make($newPassword),
            ])->save();

            app(AdminSessionRevoker::class)
                ->revokeForUser($admin->id);
        });

        $admin->refresh();

        $this->assertTrue((bool) $admin->is_active);

        $this->assertTrue(
            Hash::check(
                $newPassword,
                $admin->password
            )
        );

        $this->assertDatabaseMissing('sessions', [
            'id' => $sessionId,
        ]);
    }

    public function test_revoked_session_id_cannot_authenticate_admin(): void
    {
        $admin = $this->makeAdmin();

        $sessionId = Str::random(40);

        $this->createAuthenticatedSession(
            $admin,
            $sessionId
        );

        app(AdminSessionRevoker::class)
            ->revokeForUser($admin->id);

        $this->assertDatabaseMissing('sessions', [
            'id' => $sessionId,
        ]);

        // Reset authentication state before simulating
        // a separate request using the old session ID.
        auth()->guard('web')->forgetUser();

        $cookieName = config('session.cookie');

        $response = $this
            ->withUnencryptedCookie(
                $cookieName,
                $sessionId
            )
            ->get(route('admin.dashboard'));

        $this->assertContains(
            $response->getStatusCode(),
            [302, 401, 403]
        );

        $this->assertNotEquals(
            200,
            $response->getStatusCode()
        );
    }

    public function test_revocation_does_not_delete_other_admin_sessions(): void
    {
        $target = $this->makeAdmin();
        $other = $this->makeAdmin();

        $targetSessionId = Str::random(40);
        $otherSessionId = Str::random(40);

        $this->createAuthenticatedSession(
            $target,
            $targetSessionId
        );

        $this->createAuthenticatedSession(
            $other,
            $otherSessionId
        );

        app(AdminSessionRevoker::class)
            ->revokeForUser($target->id);

        $this->assertDatabaseMissing('sessions', [
            'id' => $targetSessionId,
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => $otherSessionId,
            'user_id' => $other->id,
        ]);
    }
}

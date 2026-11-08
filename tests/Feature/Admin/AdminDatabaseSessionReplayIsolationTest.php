<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminDatabaseSessionReplayIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $user = User::factory()->create([
            'password' => Hash::make('SecurePassword123!'),
        ]);

        $user->forceFill([
            'role' => 'admin',
            'is_super_admin' => false,
            'admin_level' => 20,
            'admin_permissions' => ['products.manage'],
            'is_active' => true,
        ])->save();

        return $user;
    }

    public function test_destroyed_database_session_cannot_restore_admin_access(): void
    {
        $admin = $this->makeAdmin();

        // Create a real database-backed session.
        $manager = app('session');
        $store = $manager->driver('database');

        $this->assertInstanceOf(
            DatabaseSessionHandler::class,
            $store->getHandler()
        );

        $store->setId(Str::random(40));
        $store->start();

        // Obtain Laravel's actual session authentication key.
        $guard = auth()->guard('web');

        $method = new \ReflectionMethod(
            $guard,
            'getName'
        );

        $loginKey = $method->invoke($guard);

        // Store the admin's authentication identifier.
        $store->put($loginKey, $admin->getAuthIdentifier());

        $oldSessionId = $store->getId();

        $store->save();

        $this->assertDatabaseHas('sessions', [
            'id' => $oldSessionId,
        ]);

        // Invalidate the database-backed session.
        $store->invalidate();

        $this->assertDatabaseMissing('sessions', [
            'id' => $oldSessionId,
        ]);

        // Reset the cached authenticated user.
        $guard->forgetUser();

        // Configure the next HTTP request to use database sessions.
        config()->set('session.driver', 'database');

        app('session')->forgetDrivers();

        // Attempt to replay the deleted session identifier.
        $this->withUnencryptedCookie(
            config('session.cookie'),
            $oldSessionId
        );

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }
}

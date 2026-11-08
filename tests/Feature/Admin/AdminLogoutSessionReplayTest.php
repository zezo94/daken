<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLogoutSessionReplayTest extends TestCase
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

    public function test_logout_blocks_replay_of_old_database_session(): void
    {
        config()->set('session.driver', 'database');

        // Ensure the session manager resolves a database-backed store.
        $manager = app('session');
        $manager->forgetDrivers();

        $store = $manager->driver('database');

        $this->assertInstanceOf(
            DatabaseSessionHandler::class,
            $store->getHandler()
        );

        $admin = $this->makeAdmin();

        // 1. Log in through the real Fortify HTTP endpoint.
        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'SecurePassword123!',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($admin);

        $oldSessionId = session()->getId();

        $this->assertNotEmpty($oldSessionId);

        // 2. Log out through Fortify.
        $this->post(route('logout'))->assertRedirect();

        $this->assertGuest();

        // 3. Clear the authentication guard's cached user.
        Auth::guard('web')->forgetUser();

        // 4. Send the old session ID as a new request cookie.
        $this->withUnencryptedCookie(
            config('session.cookie'),
            $oldSessionId
        );

        $response = $this->get(route('admin.dashboard'));

        // The old session must not restore admin authentication.
        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }
}

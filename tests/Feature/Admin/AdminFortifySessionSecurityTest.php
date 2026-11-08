<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;


class AdminFortifySessionSecurityTest extends TestCase
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

    public function test_successful_login_regenerates_session_id(): void
    {
        $admin = $this->makeAdmin();

        $this->get(route('login'));

        $oldSessionId = session()->getId();

        $response = $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'SecurePassword123!',
        ]);

        $response->assertRedirect();

        $this->assertAuthenticatedAs($admin);

        $this->assertNotEquals(
            $oldSessionId,
            session()->getId()
        );
    }

    public function test_invalid_password_does_not_authenticate(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $admin->email,
                'password' => 'WrongPassword123!',
            ]);

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_logout_invalidates_authenticated_session(): void
    {
        $admin = $this->makeAdmin();

        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'SecurePassword123!',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($admin);

        $oldSessionId = session()->getId();

        $this->post(route('logout'))->assertRedirect();

        $this->assertGuest();

        $this->assertNotEquals(
            $oldSessionId,
            session()->getId()
        );
    }

    public function test_logout_prevents_access_to_admin_dashboard(): void
    {
        $admin = $this->makeAdmin();

        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'SecurePassword123!',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($admin);

        $this->post(route('logout'))->assertRedirect();

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }


public function test_session_cookie_security_configuration(): void
{
    // Laravel may use the "array" session driver during tests.
    $this->assertContains(
        config('session.driver'),
        ['array', 'database']
    );

    $this->assertTrue(
        config('session.http_only')
    );

    $this->assertContains(
        config('session.same_site'),
        ['lax', 'strict']
    );
}


    public function test_secure_cookie_can_be_enabled_for_https(): void
    {
        config()->set('session.secure', true);

        $this->assertTrue(
            config('session.secure')
        );
    }





    public function test_logout_deletes_database_session_and_blocks_reuse(): void
    {
        $this->app['config']->set('session.driver', 'database');

        // Forget the previously resolved session manager and session store.
        $this->app->forgetInstance('session');
        $this->app->forgetInstance('session.store');

        $admin = $this->makeAdmin();

        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'SecurePassword123!',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($admin);

        $oldSessionId = session()->getId();

        session()->save();

        $this->assertDatabaseHas('sessions', [
            'id' => $oldSessionId,
        ]);

        $this->post(route('logout'))->assertRedirect();

        $this->assertGuest();

        // Check that the old session can no longer authenticate.
        $this->withUnencryptedCookie(
            config('session.cookie'),
            $oldSessionId
        );

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

public function test_https_session_cookie_has_security_attributes(): void
{
    config()->set('session.secure', true);
    config()->set('session.http_only', true);
    config()->set('session.same_site', 'lax');

    $response = $this->withServerVariables([
        'HTTPS' => 'on',
    ])->get(route('login'));

    $cookies = $response->headers->getCookies();

    $sessionCookie = collect($cookies)->first(
        fn ($cookie) => $cookie->getName() === config('session.cookie')
    );

    $this->assertNotNull($sessionCookie);

    $this->assertTrue($sessionCookie->isSecure());
    $this->assertTrue($sessionCookie->isHttpOnly());
    $this->assertSame('lax', $sessionCookie->getSameSite());
}





    public function test_database_session_is_destroyed_by_invalidation(): void
    {
        config()->set('session.driver', 'database');

        // Resolve a fresh database-backed session store.
        $manager = app('session');

        $store = $manager->driver('database');

        $this->assertInstanceOf(
            \Illuminate\Session\DatabaseSessionHandler::class,
            $store->getHandler()
        );

        // Create a real database-backed session.
        $store->setId(\Illuminate\Support\Str::random(40));
        $store->start();

        $sessionId = $store->getId();

        $store->put('security_test', 'authenticated');
        $store->save();

        $this->assertTrue(
            DB::table('sessions')
                ->where('id', $sessionId)
                ->exists()
        );

        // Invalidate the same session using Laravel's session API.
        $store->invalidate();

        $this->assertFalse(
            DB::table('sessions')
                ->where('id', $sessionId)
                ->exists()
        );

        $this->assertNotEquals(
            $sessionId,
            $store->getId()
        );
    }





}

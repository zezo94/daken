<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Tests\TestCase;

class AdminPasskeysDisabledTest extends TestCase
{
    public function test_passkeys_feature_is_disabled(): void
    {
        $this->assertFalse(
            Features::enabled(
                Features::passkeys()
            )
        );
    }

    public function test_passkey_login_routes_are_not_registered(): void
    {
        $this->assertFalse(
            Route::has('passkey.login')
        );

        $this->assertFalse(
            Route::has('passkey.login-options')
        );
    }

    public function test_passkey_management_routes_are_not_registered(): void
    {
        $this->assertFalse(
            Route::has('passkey.store')
        );

        $this->assertFalse(
            Route::has('passkey.destroy')
        );

        $this->assertFalse(
            Route::has('passkey.registration-options')
        );
    }
}

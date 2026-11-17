<?php

namespace App\Providers;

use App\Models\User;
use App\Services\AdminSessionRevoker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Events\TwoFactorAuthenticationConfirmed;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $revokeOtherSessions = function ($event): void {
            $user = $event->user;

            if (! $user instanceof User || $user->role !== 'admin') {
                return;
            }

            $request = request();

            if (! $request->hasSession()
                || ! auth('web')->check()
                || (int) auth('web')->id() !== (int) $user->id) {
                return;
            }

            $user->forceFill([
                'remember_token' => Str::random(60),
            ])->save();

            app(AdminSessionRevoker::class)->revokeOtherSessions(
                (int) $user->id,
                $request->session()->getId()
            );
        };

        Event::listen(
            TwoFactorAuthenticationConfirmed::class,
            $revokeOtherSessions
        );

        Event::listen(
            TwoFactorAuthenticationDisabled::class,
            $revokeOtherSessions
        );
    }
}

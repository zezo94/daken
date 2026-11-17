<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Responses\StaffPasswordResetLinkResponse;
use App\Models\User;
use App\Support\AdminLoginPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Laravel\Fortify\Fortify;
use App\Http\Responses\StaffPasswordUpdateResponse;
use Laravel\Fortify\Contracts\PasswordUpdateResponse;
use App\Http\Responses\StaffPasswordResetResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse;
use App\Http\Responses\StaffProfileInformationUpdatedResponse;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse;





class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register Fortify response bindings.
     */
    public function register(): void
    {
        $this->app->bind(
            SuccessfulPasswordResetLinkRequestResponse::class,
            StaffPasswordResetLinkResponse::class
        );

        $this->app->bind(
            FailedPasswordResetLinkRequestResponse::class,
            StaffPasswordResetLinkResponse::class
        );

        $this->app->bind(
            PasswordUpdateResponse::class,
            StaffPasswordUpdateResponse::class
        );


        $this->app->bind(
            PasswordResetResponse::class,
            StaffPasswordResetResponse::class
        );

        $this->app->bind(
            ProfileInformationUpdatedResponse::class,
            StaffProfileInformationUpdatedResponse::class
        );


    }

    /**
     * Configure staff authentication and Fortify.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Fortify Actions
        |--------------------------------------------------------------------------
        */

        Fortify::createUsersUsing(
            CreateNewUser::class
        );

        Fortify::updateUserProfileInformationUsing(
            UpdateUserProfileInformation::class
        );

        Fortify::updateUserPasswordsUsing(
            UpdateUserPassword::class
        );

        Fortify::resetUserPasswordsUsing(
            ResetUserPassword::class
        );

        Fortify::redirectUserForTwoFactorAuthenticationUsing(
            RedirectIfTwoFactorAuthenticatable::class
        );

        /*
        |--------------------------------------------------------------------------
        | Password Reset Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for(
            'staff-password-reset',
            function (Request $request) {
                return Limit::perMinutes(10, 5)
                    ->by($request->ip());
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Login Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower(
                    (string) $request->input(
                        Fortify::username()
                    )
                ).'|'.$request->ip()
            );

            return Limit::perMinute(5)
                ->by($throttleKey);
        });

        /*
        |--------------------------------------------------------------------------
        | Two-Factor Authentication Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for('two-factor', function (Request $request) {
            $pendingUserId = $request->session()->get('login.id');

            $identifier = $pendingUserId !== null
                ? 'user:'.$pendingUserId
                : 'session:'.$request->session()->getId();

            return Limit::perMinute(5)
                ->by($identifier);
        });

        /*
        |--------------------------------------------------------------------------
        | Passkey Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId())
                .'|'.$request->ip()
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Authentication Views
        |--------------------------------------------------------------------------
        */

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::confirmPasswordView(function () {
            return view('auth.confirm-password');
        });

        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(function (Request $request) {
            return view('auth.reset-password', [
                'request' => $request,
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Active Admin Authentication
        |--------------------------------------------------------------------------
        |
        | Only active administrator accounts may authenticate.
        | The same policy is checked during the pending 2FA challenge
        | by the middleware configured in config/fortify.php.
        |
        */

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::query()
                ->where(
                    'email',
                    $request->input('email')
                )
                ->first();

            if (! $user) {
                return null;
            }

            if (! AdminLoginPolicy::allows($user)) {
                return null;
            }

            if (! Hash::check(
                (string) $request->input('password'),
                $user->password
            )) {
                return null;
            }

            return $user;
        });

    }
}

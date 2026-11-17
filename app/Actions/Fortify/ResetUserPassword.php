<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\AdminSessionRevoker;
use App\Support\AdminLoginPolicy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param array<string, string> $input
     *
     * @throws ValidationException
     */
    public function reset(User $user, array $input): void
    {
        if (! AdminLoginPolicy::allows($user)) {
            throw ValidationException::withMessages([
                'email' => __('Unable to reset the password for this account.'),
            ]);
        }

        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
            'remember_token' => Str::random(60),
        ])->save();

        app(AdminSessionRevoker::class)
            ->revokeForUser($user->id);
    }
}

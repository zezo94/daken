<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\AdminSessionRevoker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use App\Support\AdminLoginPolicy;









class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        abort_unless(AdminLoginPolicy::allows($user), 403);
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
            'remember_token' => Str::random(60),
        ])->save();


        if ($user->role === 'admin') {
            app(AdminSessionRevoker::class)->revokeForUser($user->id);
        }
    }
}

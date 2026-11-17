<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Support\AdminLoginPolicy;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use App\Services\AdminSessionRevoker;
use Illuminate\Support\Str;










class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
        abort_unless(AdminLoginPolicy::allows($user), 403);

        Validator::make($input, [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'current_password' => [
                'required',
                'string',
            ],
        ])->validateWithBag('updateProfileInformation');

        if (! Hash::check(
            (string) $input['current_password'],
            $user->password
        )) {
            throw ValidationException::withMessages([
                'current_password' => 'كلمة المرور الحالية غير صحيحة.',
            ])->errorBag('updateProfileInformation');
        }

        if ($input['email'] === $user->email) {
            return;
        }

        if ($user instanceof MustVerifyEmail) {
            $user->forceFill([
                'email' => $input['email'],
                'email_verified_at' => null,
                'remember_token' => Str::random(60),
            ])->save();

            $user->sendEmailVerificationNotification();
        } else {
            $user->forceFill([
                'email' => $input['email'],
                'remember_token' => Str::random(60),
            ])->save();
        }

        app(AdminSessionRevoker::class)->revokeForUser($user->id);
    }
}

<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
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
            throw \Illuminate\Validation\ValidationException::withMessages([
                'current_password' => 'كلمة المرور الحالية غير صحيحة.',
            ])->errorBag('updateProfileInformation');
        }

        // Only email is accepted. Never update name, role,
        // admin level, permissions, or account status.
        if ($input['email'] === $user->email) {
            return;
        }

        if ($user instanceof MustVerifyEmail) {
            $user->forceFill([
                'email' => $input['email'],
                'email_verified_at' => null,
            ])->save();

            $user->sendEmailVerificationNotification();

            return;
        }

        $user->forceFill([
            'email' => $input['email'],
        ])->save();
    }
}

<?php

namespace App\Support;

use App\Models\User;

final class AdminLoginPolicy
{
    public static function allows(User $user): bool
    {
        return $user->role !== 'admin'
            || $user->is_active === true;
    }
}

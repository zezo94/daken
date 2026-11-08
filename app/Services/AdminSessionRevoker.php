<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AdminSessionRevoker
{
    public function revokeForUser(int $userId): int
    {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->delete();
    }
}

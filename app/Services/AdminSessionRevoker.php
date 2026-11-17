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

    public function revokeOtherSessions(
        int $userId,
        string $currentSessionId
    ): int {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }
}

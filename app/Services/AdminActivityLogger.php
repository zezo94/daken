<?php

namespace App\Services;

use App\Models\AdminActivity;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLogger
{
    public function log(
        string $action,
        ?Model $subject = null,
        array $details = []
    ): void {
        AdminActivity::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->hasAdminPermission('users.manage');
    }

    public function view(User $actor, User $target): bool
    {
        return $actor->canManageUser($target);
    }

    public function create(User $actor): bool
    {
        return $actor->hasAdminPermission('users.manage');
    }

    public function update(User $actor, User $target): bool
    {
        return $actor->canManageUser($target);
    }

    public function delete(User $actor, User $target): bool
    {
        return $actor->canManageUser($target);
    }
}

<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, Service $service): bool
    {
        return $user->exists;
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->exists;
    }
}

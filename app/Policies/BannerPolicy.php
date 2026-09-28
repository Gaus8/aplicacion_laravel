<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;

class BannerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, Banner $banner): bool
    {
        return $user->exists;
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, Banner $banner): bool
    {
        return $user->exists;
    }

    public function delete(User $user, Banner $banner): bool
    {
        return $user->exists;
    }
}

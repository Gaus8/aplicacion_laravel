<?php

namespace App\Policies;

use App\Models\SocialLink;
use App\Models\User;

class SocialLinkPolicy
{
    public function viewAny(User $user): bool { return $user->exists; }
    public function create(User $user): bool { return $user->exists; }
    public function update(User $user, SocialLink $link): bool { return $user->exists; }
    public function delete(User $user, SocialLink $link): bool { return $user->exists; }
}

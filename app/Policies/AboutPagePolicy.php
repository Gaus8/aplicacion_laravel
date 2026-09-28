<?php

namespace App\Policies;

use App\Models\AboutPage;
use App\Models\User;

class AboutPagePolicy
{
    public function viewAny(User $user): bool { return $user->exists; }
    public function update(User $user, AboutPage $page): bool { return $user->exists; }
}

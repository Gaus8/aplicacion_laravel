<?php

namespace App\Policies;

use App\Models\TeamMember;
use App\Models\User;

class TeamMemberPolicy
{
    public function viewAny(User $user): bool { return $user->exists; }
    public function create(User $user): bool { return $user->exists; }
    public function update(User $user, TeamMember $member): bool { return $user->exists; }
    public function delete(User $user, TeamMember $member): bool { return $user->exists; }
}

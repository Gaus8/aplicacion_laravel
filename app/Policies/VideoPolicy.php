<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;

class VideoPolicy
{
    public function viewAny(User $user): bool { return $user->exists; }
    public function create(User $user): bool { return $user->exists; }
    public function update(User $user, Video $video): bool { return $user->exists; }
    public function delete(User $user, Video $video): bool { return $user->exists; }
}

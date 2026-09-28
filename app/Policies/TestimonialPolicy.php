<?php

namespace App\Policies;

use App\Models\Testimonial;
use App\Models\User;

class TestimonialPolicy
{
    public function viewAny(User $user): bool { return $user->exists; }
    public function create(User $user): bool { return $user->exists; }
    public function update(User $user, Testimonial $testimonial): bool { return $user->exists; }
    public function delete(User $user, Testimonial $testimonial): bool { return $user->exists; }
}

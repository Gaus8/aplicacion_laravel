<?php

namespace App\Policies;

use App\Models\ContactSubmission;
use App\Models\User;

class ContactSubmissionPolicy
{
    public function viewAny(User $user): bool { return $user->exists; }
    public function update(User $user, ContactSubmission $submission): bool { return $user->exists; }
}

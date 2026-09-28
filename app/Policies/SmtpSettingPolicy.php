<?php

namespace App\Policies;

use App\Models\SmtpSetting;
use App\Models\User;

class SmtpSettingPolicy
{
    public function viewAny(User $user): bool { return $user->exists; }
    public function update(User $user, SmtpSetting $setting): bool { return $user->exists; }
    public function test(User $user): bool { return $user->exists; }
}

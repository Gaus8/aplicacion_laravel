<?php

namespace App\Policies;

use App\Models\SeoSetting;
use App\Models\User;

class SeoSettingPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('seo.manage'); }
    public function update(User $user, SeoSetting $setting): bool { return $user->hasPermission('seo.manage'); }
}

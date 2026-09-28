<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Role;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::created(function (User $user): void {
            $administrator = Role::query()->where('slug', 'administrador')->first();
            if ($administrator) $user->roles()->syncWithoutDetaching([$administrator->id]);
        });
    }
}

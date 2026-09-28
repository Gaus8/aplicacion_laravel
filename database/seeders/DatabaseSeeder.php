<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Seed professional public-facing example content. */
    public function run(): void
    {
        $this->call(CompanyContentSeeder::class);
    }
}

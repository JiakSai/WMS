<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ModuleManagementSeeder::class);
        $this->call(OrganisationManagementSeeder::class);
        $this->call(UserManagementSeeder::class);
    }
}

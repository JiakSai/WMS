<?php

namespace Database\Seeders;

use App\Models\Sys\Usrm\SysUsrmGrpcs;
use App\Models\Sys\Usrm\SysUsrmUsers;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = SysUsrmUsers::create([
            'username' => '00000',
            'password' => Hash::make('123456'),
            'name' => 'Default User',
            'email_address' => 'default@esmtt.com',
            'group' => 1,
            'default_organisation' => 1, // Set default organization
        ]);

        // Attach organizations
        $timestamp = Carbon::now()->format('Y-m-d H:i:s.u');
        $user->organisations()->attach([1], ['created_at' => $timestamp, 'updated_at' => $timestamp]);

        $group = SysUsrmGrpcs::create([
            'name' => 'IT',
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("users")->insert([
            'username' => '00000',
            'name' => 'default',
            'password' => Hash::make('123456'),
            'phone_number' => '0123456789',
            'email_address' => 'default@esmtt.com',
            'redirect_url' => '12',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('warehouses')->insert([
            'name' => 'default',
            'created_by' => 'default',
            'updated_by' => 'default',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('user_warehouse')->insert([
            'user_id' => 1,
            'warehouse_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
             
    }
}

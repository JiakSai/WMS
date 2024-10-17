<?php

namespace Database\Seeders;

use App\Models\Sys\Orga\SysOrgaCtrls;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganisationManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organisation = SysOrgaCtrls::create([
            'name' => 'DEFAULT',
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);
    }
}

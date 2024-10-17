<?php

namespace Database\Seeders;

use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Modu\SysModuSubms;
use App\Models\Sys\Modu\SysModuTabms;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainModule = SysModuMains::create([

            'name' => 'System',
            'icon' => 'bi bi-motherboard',
            'code' => 'sys',
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);

        $moduleManagement = SysModuSubms::create([
            'name' => 'Module Management',
            'code' => 'sys_modu',
            'description' => 'System Module Management',
            'route' => 'sys.modu.index',
            'mobile' => '1',
            'icon' => 'bi bi-sliders2',
            'group' => $mainModule->id,
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);

        $userManagement = SysModuSubms::create([
            'name' => 'User Management',
            'code' => 'sys_usrm',
            'description' => 'System User Management',
            'route' => 'sys.usrm.index',
            'mobile' => '1',
            'icon' => 'bi bi-person-fill-gear',
            'group' => $mainModule->id,
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);

        $organisationManagement = SysModuSubms::create([
            'name' => 'Organisation Management',
            'code' => 'sys_orga',
            'description' => 'System Organisation Management',
            'route' => 'sys.orga.index',
            'mobile' => '1',
            'icon' => 'bi bi-building-gear',
            'group' => $mainModule->id,
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);

        $mainModuleControl = SysModuTabms::create([
            'name' => 'Main Module Control',
            'main_group' => $mainModule->id,
            'sub_group' => $moduleManagement->id,
            'route' => 'sys.modu.mains.index',
            'code' => 'sys_modu_mains',
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);

        $subModuleControl = SysModuTabms::create([
            'name' => 'Sub Module Control',
            'main_group' => $mainModule->id,
            'sub_group' => $moduleManagement->id,
            'route' => 'sys.modu.subms.index',
            'code' => 'sys_modu_subms',
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);

        $tabModuleControl = SysModuTabms::create([
            'name' => 'Tab Module Control',
            'main_group' => $mainModule->id,
            'sub_group' => $moduleManagement->id,
            'route' => 'sys.modu.tabms.index',
            'code' => 'sys_modu_tabms',
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);

        $userControl = SysModuTabms::create([
            'name' => 'User Control',
            'main_group' => $mainModule->id,
            'sub_group' => $userManagement->id,
            'route' => 'sys.usrm.users.index',
            'code' => 'sys_usrm_users',
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);

        $groupControl = SysModuTabms::create([
            'name' => 'Group Control',
            'main_group' => $mainModule->id,
            'sub_group' => $userManagement->id,
            'route' => 'sys.usrm.grpcs.index',
            'code' => 'sys_usrm_grpcs',
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);

        $organisationControl = SysModuTabms::create([
            'name' => 'Organisation Control',
            'main_group' => $mainModule->id,
            'sub_group' => $organisationManagement->id,
            'route' => 'sys.orga.ctrls.index',
            'code' => 'sys_orga_ctrls',
            'created_by' => 'CHEW JUN JIE',
            'updated_by' => 'CHEW JUN JIE',
        ]);
    }
}

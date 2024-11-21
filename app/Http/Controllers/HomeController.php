<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Modu\SysModuSubms;
use App\Models\Sys\Modu\SysModuTabms;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Wms\Role\WmsRolePerms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index(SysOrgaCtrls $organisation, Request $request)
    {
        $permissions = Session::get('main_modules_permission', []);

        $mainModules = SysModuMains::whereIn('id', $permissions)->get();
    
        $subModules = SysModuSubms::get();
    
        return view('home', compact('mainModules', 'subModules', 'organisation'));
    }

    public function selectModule(SysOrgaCtrls $organisation, SysModuSubms $subModule)
    {
        if(!$subModule)
        {
            return redirect()->route('home', ['organisation' => $organisation]);
        }

        if(! $subModule->is_active && Auth::user()->group != 1)
        {
            abort(503);
        }

        $main = SysModuMains::where('id', $subModule->group)->first();
        
        // // Retrieve all tabs for the sub-module
        $tabs = SysModuTabms::where('sub_group', $subModule->id)->get();

        // // Get user's role IDs from the session
        // $userRoleIds = session('user_role_ids', []);

        // if (!empty($userRoleIds)) {
        //     // Get IDs of all tabs
        //     $tabIds = $tabs->pluck('id')->toArray();

        //     // Retrieve all 'view' permissions for the given tab IDs
        //     $permissions = WmsRolePerms::whereIn('tab_module', $tabIds)
        //         ->where('type', 'view')
        //         ->get();

        //     // Filter permissions where the user has the role
        //     $permittedTabIds = $permissions->filter(function ($perm) use ($userRoleIds) {
        //         if (is_array($perm->role) && !empty($perm->role)) {
        //             return !empty(array_intersect($perm->role, $userRoleIds));
        //         } else {
        //             // No roles assigned to this permission; deny access
        //             return false;
        //         }
        //     })->pluck('tab_module')
        //     ->unique()
        //     ->toArray();

        //     // Filter tabs
        //     $tabs = $tabs->whereIn('id', $permittedTabIds)->values();
        // } else {
        //     // User has no roles; they shouldn't see any tabs
        //     $tabs = collect();
        // }

        // if ($tabs->isEmpty()) {
        //     abort(403);
        // }

        return view($subModule->route, compact(['organisation', 'tabs', 'main']));
    }

}

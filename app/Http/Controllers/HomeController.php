<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Modu\SysModuSubms;
use App\Models\Sys\Modu\SysModuTabms;
use App\Models\Sys\Orga\SysOrgaCtrls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(SysOrgaCtrls $organisation, Request $request)
    {
        $mainModules = SysModuMains::where ('is_active', 1)->get();

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

        $tabs = SysModuTabms::where('sub_group', $subModule->id)->get();

        return view($subModule->route, compact(['organisation', 'tabs']));
    }

}

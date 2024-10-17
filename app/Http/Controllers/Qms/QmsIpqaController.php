<?php

namespace App\Http\Controllers\Sys;

use App\Http\Controllers\Controller;
use App\Models\Sys\Modu\SysModuSubms;
use App\Models\Sys\Modu\SysModuTabms;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SysUsrmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        $subModule = SysModuSubms::find(session('subModule'));
        
        if (!$subModule) {
            return redirect()->route('home', $organisation->id);
        }

        $tabs = SysModuTabms::where('sub_group', $subModule->id)->get();

        return view('qms.ipqa.index', compact('organisation', 'tabs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

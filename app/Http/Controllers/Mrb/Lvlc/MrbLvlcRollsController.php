<?php

namespace App\Http\Controllers\Mrb\Lvlc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mrb\Lvlc\MrbLvlcRolls;
use App\Models\Sys\Orga\SysOrgaCtrls;

class MrbLvlcRollsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('mrb.lvlc.rolls.index', compact('organisation'));
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
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $roleName = $request->input('name', '');  // Capture role_name input

        // Query QmsRoleRolls model, filtered by organisation_id
        $query = MrbLvlcRolls::where('organisation_id', $organisation->id);

        // Filter by role_name if provided
        if (!empty($roleName)) {
            $query->where('level', 'LIKE', "%{$roleName}%");
        }
        
        // Total and filtered records count
        $totalRecords = MrbLvlcRolls::where('organisation_id', $organisation->id)->count();
        $filteredRecords = $query->count();

        // Fetch filtered results with pagination
        $rolesData = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();

        // Map data for response
        $rolesData = $rolesData->map(function ($item) {
            return [
                'id' => $item->id,
                'level' => $item->level,
                'descriptions' => $item->descriptions,
                'created_by' => $item->created_by,
                'updated_by' => $item->updated_by,
                'max_users' => $item->max_users // Add the count of assignments
            ];
        });

        // Prepare JSON response
        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $rolesData
        ];

        return response()->json($json_data);
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

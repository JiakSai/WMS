<?php

namespace App\Http\Controllers\Sys\Usrm;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SysUsrmRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('sys.usrm.roles.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        return response()->json(['data' => view('sys.usrm.roles.create', compact('organisation'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' =>'required',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $group = SysUsrmRoles::create([
            'name' => $request->name,
            'organisation_id' => $organisation->id,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2 , 'message' => 'User Role added successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = SysUsrmRoles::where('organisation_id', $organisation->id);

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = SysUsrmRoles::count();
        $filteredRecords = $query->count();

        $data = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $data = $data->map(function ($item) {

            return [
                'id' => $item->id,
                'name' => $item->name,
                'created_by' => $item->created_by,
                'updated_by' => $item->updated_by,
            ];

        }); 

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ];

        return response()->json($json_data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SysOrgaCtrls $organisation, Request $request)
    {
        $role = SysUsrmRoles::find($request->id);

        if(!$role){
            return response()->json(['status' => 1, 'message' => 'User Role not found']);
        }

        return response()->json(['data' => view('sys.usrm.roles.edit', compact('role', 'organisation'))->render()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }
        
        $role = SysUsrmRoles::find($request->id);

        if(!$role){
            return response()->json(['status' => 1, 'message' => 'User role not found']);
        }

        SysUsrmRoles::where('id', $role->id)->update([
            'name' => $request->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'User role updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $role = SysUsrmRoles::find($request->id);

        if(!$role){
            return response()->json([ 'status' => 1, 'message' => 'Role not found',]);
        }

        $role->delete();

        return response()->json(['status' => 2, 'message'=> 'Remove role successfully!']);
    }
}

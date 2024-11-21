<?php

namespace App\Http\Controllers\Sys\Role;

use App\Http\Controllers\Controller;
use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Role\SysRoleAuths;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SysRoleAuthsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('sys.role.auths.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $main_modules = SysModuMains::get();

        return response()->json(['data' => view('sys.role.auths.create', compact('organisation', 'main_modules'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'type' => 'required',
            'main_module' => 'required',
            'name' =>'required|unique:sys_role_auths,name',
        ], [
            'name.unique' => 'The Permission Already Exist !',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        SysRoleAuths::create([
            'type' => $request->type,
            'main_module' => $request->main_module,
            'name' => $request->name,
            'description' => $request->description,
            'organisation_id' => $organisation->id,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2 , 'message' => 'Permission added successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = SysRoleAuths::where('organisation_id', $organisation->id);

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = SysRoleAuths::count();
        $filteredRecords = $query->count();

        $data = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $data = $data->map(function ($item) {

            return [
                'id' => $item->id,
                'permission' => $item->type,
                'main_module' => SysModuMains::where('id', $item->main_module)->first()->name,
                'description' => $item->description,
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
        $main_modules = SysModuMains::get();
        
        $permission = SysRoleAuths::find($request->id);

        if(!$permission)
        {
            return response()->json(['status' => 1, 'message' => 'Permissions not found']);
        }

        return response()->json(['data' => view('sys.role.auths.edit', compact('main_modules', 'permission', 'organisation'))->render()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:sys_role_auths,name,'. $request->id,
        ], [
            'name.unique' => 'The Permission Already Exist !',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $permission = SysRoleAuths::find($request->id);

        if(!$permission)
        {
            return response()->json(['status' => 1, 'message' => 'Permission not found !']);
        }

        SysRoleAuths::find($request->id)->update([
            'description' => $request->description
        ]);

        return response()->json(['status' => 2, 'message' => 'Permission update successfully !']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $permission = SysRoleAuths::find($request->id);

        if(!$permission)
        {
            return response()->json(['status' => 1, 'message' => 'Permission not found !']);
        }

        $permission->delete();

        return response()->json(['status' => 2, 'message' => 'Permission delete successfully !']);
    }
}

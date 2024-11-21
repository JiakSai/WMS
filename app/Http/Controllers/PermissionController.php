<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Modu\SysModuTabms;
use App\Models\Sys\Orga\SysOrgaCtrls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation, Request $request)
    {
        $mainModule = SysModuMains::find($request->main_id);

        if(!$mainModule)
        {
            return response()->json(['status' => 1, 'message' => 'Main Module not found']);
        }

        return view('rbp.perm.index' , compact('organisation', 'mainModule'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation, SysModuMains $mainModule, Request $request)
    {
        $permission = Permission::where('id', $request->id)->first();

        $roles = Role::where('organisation_id', $organisation->id)->get();
    
        $permissionRoles = $permission->role ?? [];
    
        $permissionRoles = is_array($permissionRoles) ? $permissionRoles : [];

        $selectedRoles = Role::whereIn('id', $permissionRoles)->pluck('id')->toArray();

        return response()->json([ 'data' => view('rbp.perm.create', compact('organisation', 'roles', 'permission', 'selectedRoles', 'mainModule'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        
        if( $validate->fails() ){
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $permission = Permission::find($request->id);

        if(!$permission)
        {
            return response()->json(['status' => 1, 'message' => 'Permission not found']);
        }

        Permission::find($request->id)->update([
            'role' => $request['role']
        ]);

        return response()->json(['status' => 2, 'message' => 'Role Assign Successfully ! ']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        // Retrieve request parameters with default values
        $draw = intval($request->input('draw', 1));
        $start = intval($request->input('start', 0));
        $length = intval($request->input('length', 10));
        $name = $request->input('name', '');

        if($request->main_id == 1)
        {
            $query = Permission::where('organisation_id', $organisation->id);
        }else{
            $query = Permission::where('organisation_id', $organisation->id)->where('main_module', $request->main_id);
        }
        
        // Apply name filter if provided
        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        // Get total records
        $totalRecords = Permission::where('organisation_id', $organisation->id)->where('main_module', $request->main_id)->count();

        // Get filtered records
        $filteredRecords = $query->count();

        // Fetch the paginated data
        $data = $query->orderBy('id', 'DESC')
            ->offset($start)
            ->limit($length)
            ->get();

        // Extract all unique role IDs from the fetched permissions
        $allRoleIds = $data->pluck('role')->flatten()->unique()->filter()->values();

        // Fetch role names corresponding to the IDs
        $rolesMap = [];
        if ($allRoleIds->isNotEmpty()) {
            $rolesMap = Role::whereIn('id', $allRoleIds)->pluck('name', 'id')->toArray();
        }

        // Map permissions data to include role names
        $formattedData = $data->map(function ($item) use ($rolesMap) {
            // Decode the roles JSON array
            $roleIds = is_array($item->role) ? $item->role : json_decode($item->role, true) ?? [];

            // Map role IDs to names
            $roleNames = collect($roleIds)
                ->map(function ($id) use ($rolesMap) {
                    return $rolesMap[$id] ?? 'Unknown';
                })
                ->filter() // Remove any 'Unknown' if desired
                ->values()
                ->all();

            // Convert the array of role names to a comma-separated string
            $rolesString = implode(', ', $roleNames);

            return [
                'id' => $item->id,
                'permission' => $item->type, // Assuming 'type' represents the permission
                'tab_module' => SysModuTabms::where('id', $item->tab_module)->first()->name,
                'main_module' => SysModuMains::where('id', $item->main_module)->first()->name,
                'name' => $item->name,
                'description' => $item->description,
                'roles' => $rolesString,
                'created_by' => $item->created_by,
                'updated_by' => $item->updated_by,
            ];
        });

        // Prepare the JSON response for DataTables
        $json_data = [
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $formattedData
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

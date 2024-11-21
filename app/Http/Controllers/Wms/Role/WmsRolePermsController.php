<?php

namespace App\Http\Controllers\Wms\Role;

use App\Http\Controllers\Controller;
use App\Models\Sys\Modu\SysModuTabms;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Wms\Role\WmsRoleAsgns;
use App\Models\Wms\Role\WmsRolePerms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WmsRolePermsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('wms.role.perms.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation, Request $request)
    {
        $permission = WmsRolePerms::where('id', $request->id)->first();

        $roles = WmsRoleAsgns::where('organisation_id', $organisation->id)->get();
    
        $permissionRoles = $permission->role ?? [];
    
        $permissionRoles = is_array($permissionRoles) ? $permissionRoles : [];

        $selectedRoles = WmsRoleAsgns::whereIn('id', $permissionRoles)->pluck('id')->toArray();

        return response()->json([ 'data' => view('wms.role.perms.create', compact('organisation', 'roles', 'permission', 'selectedRoles'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {

        dd($request->all());
        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        
        if( $validate->fails() ){
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $permission = WmsRolePerms::find($request->id);

        if(!$permission)
        {
            return response()->json(['status' => 1, 'message' => 'Permission not found']);
        }

        WmsRolePerms::find($request->id)->update([
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

        // Initialize the query to fetch permissions belonging to the organization
        $query = WmsRolePerms::where('organisation_id', $organisation->id);

        // Apply name filter if provided
        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        // Get total records
        $totalRecords = WmsRolePerms::where('organisation_id', $organisation->id)->count();

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
            $rolesMap = WmsRoleAsgns::whereIn('id', $allRoleIds)->pluck('name', 'id')->toArray();
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
                'main_module' => $item->tab_module,
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

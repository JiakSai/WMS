<?php

namespace App\Http\Controllers\Sys\Role;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Role\SysRoleAuths;
use App\Models\Sys\Usrm\SysUsrmRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SysRoleAssgsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('sys.role.assgs.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $roles = SysUsrmRoles::where('organisation_id', $organisation->id)->get();

        $permissions = SysRoleAuths::where('organisation_id', $organisation->id)->get();

        return response()->json([ 'data' => view('sys.role.assgs.create', compact('organisation', 'roles', 'permissions'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        // Create a validator instance
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:sys_usrm_roles,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:sys_role_auths,id',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        // Retrieve validated data
        $validated = $validator->validated();

        // Find the role within the specified organization
        $role = SysUsrmRoles::where('organisation_id', $organisation->id)
            ->findOrFail($validated['role_id']);

        // Check if the role already has permissions
        if (!empty($role->permissions) && count($role->permissions) > 0) {
            return response()->json([
                'status' => 1,
                'errors' => ['Role already has permissions assigned'],
            ]);
        }

        // Assign permissions as an array; the model will handle JSON encoding
        $role->permissions = $validated['permissions'];
        $role->save();

        return response()->json([
            'status' => 2,
            'message' => 'Permissions assigned successfully.',
        ]);
    }

    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        // Retrieve request parameters with default values
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        // Initialize the query to fetch roles belonging to the organization
        // and ensure roles have permissions (not null and not empty)
        $query = SysUsrmRoles::where('organisation_id', $organisation->id)
            ->whereNotNull('permissions') // Exclude roles with null permissions
            ->where(function($q) {
                // Depending on how 'permissions' is stored, adjust this condition
                // If stored as JSON array:
                $q->where('permissions', '!=', '[]')->whereRaw("JSON_LENGTH(permissions) > 0");
            });

        // Apply name filter if provided
        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        // Calculate total records within the organization (with permissions)
        $totalRecords = SysUsrmRoles::where('organisation_id', $organisation->id)
            ->whereNotNull('permissions')
            ->where(function($q) {
                $q->where('permissions', '!=', '[]')->whereRaw("JSON_LENGTH(permissions) > 0");
            })
            ->count();

        // Calculate filtered records after applying name filter
        $filteredRecords = $query->count();

        // Fetch the paginated data
        $data = $query->orderBy('id', 'DESC')
            ->offset($start)
            ->limit($length)
            ->get();

        // Extract all permission IDs from the fetched roles
        // Assuming 'permissions' is stored as a JSON array in the database
        $allPermissionIds = $data->pluck('permissions')->flatten()->unique()->filter()->values();

        // Fetch permission names corresponding to the IDs
        $permissionsMap = [];
        if ($allPermissionIds->isNotEmpty()) {
            $permissionsMap = SysRoleAuths::whereIn('id', $allPermissionIds)
                ->pluck('name', 'id')
                ->toArray();
        }

        // Map roles data to include permission names
        $data = $data->map(function ($item) use ($permissionsMap) {
            // Decode permissions (ensure it's an array)
            // Adjust decoding based on how permissions are stored
            if (is_array($item->permissions)) {
                $permissionIds = $item->permissions;
            } else {
                $permissionIds = json_decode($item->permissions, true) ?? [];
            }

            // Map permission IDs to names
            $permissionNames = collect($permissionIds)
                ->map(function ($id) use ($permissionsMap) {
                    return $permissionsMap[$id] ?? 'Unknown';
                })
                ->filter() // Remove any 'Unknown' if desired
                ->values()
                ->all();

            // Convert the array of permission names to a comma-separated string
            $permissionsString = implode(', ', $permissionNames);

            return [
                'id' => $item->id,
                'permissions' => $permissionsString,
                'description' => $item->description,
                'name' => $item->name,
                'created_by' => $item->created_by,
                'updated_by' => $item->updated_by,
            ];
        });

        // Prepare the JSON response for DataTables
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
        $role = SysUsrmRoles::where('organisation_id', $organisation->id)->find($request->id);

        if(!$role)
        {
            return response()->json(['status' => 1, 'message' => 'Role Permissions not found']);
        }

        $permissions = SysRoleAuths::all();

        return response()->json([
            'data' => view('sys.role.assgs.edit', compact('organisation', 'role', 'permissions'))->render()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:sys_role_auths,id',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $role = SysUsrmRoles::where('organisation_id', $organisation->id)->find($request->role);

        if(!$role)
        {
            return response()->json(['status' => 1, 'message' => 'Role Permissions not found']);
        }

        $role->permissions = $request['permissions'];
        $role->save();

        return response()->json([
            'status' => 2,
            'message' => 'Permissions updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $role = SysUsrmRoles::find($request->id);

        $role->permissions = null;
        $role->save();

        return response()->json([
            'status' => 2,
            'message' => 'All permissions have been cleared from the role successfully.',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Qms\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmUsers;
use App\Models\Qms\Role\QmsRoleAsgns;
use App\Models\Qms\Role\QmsRoleRolls;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class QmsRoleAsgnsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('qms.role.asgns.index', compact('organisation'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $roles = QmsRoleRolls::get();
        return response()->json(['data' => view('qms.role.asgns.create', compact('organisation', 'roles'))->render()]);
    }

    public function getUsersWithoutRole(SysOrgaCtrls $organisation, Request $request)
    {
        $organisationId = $organisation->id; // Get the organisation ID from the passed model
        $searchTerm = $request->input('name'); // Get the search term from the request

        // Query to get users without a role in the specified organisation
        $users = SysUsrmUsers::leftJoin('qms_role_asgns', function ($join) use ($organisationId) {
            $join->on('sys_usrm_users.username', '=', 'qms_role_asgns.username')
                ->where('qms_role_asgns.organisation_id', $organisationId);
        })
        ->whereNull('qms_role_asgns.username') // Only select users without a role
        ->where(function ($query) use ($searchTerm) {
            if ($searchTerm) {
                $query->where('sys_usrm_users.username', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('sys_usrm_users.name', 'LIKE', '%' . $searchTerm . '%');
            }
        })
        ->get(['sys_usrm_users.username', 'sys_usrm_users.name']); // Specify the fields to retrieve

        // Check if the collection is empty
        if ($users->isEmpty()) {
            $json_data = [
                "status" => 1,
                'errors' => "No User Found"
            ]; 
        } else {
            // Prepare the user data in the desired format
            $userData = $users->map(function ($item) {
                return [
                    'username' => $item->username,
                    'name' => $item->name,
                ];
            });

            // Prepare JSON response
            $json_data = [
                "status" => 2,
                "data" => $userData
            ];            
        }

        return response()->json($json_data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required|integer',
            'role' => 'required|array',
            'role.*' => 'required|integer', // Each role ID should be an integer
        ]);
        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }
    
        // Find the user by username
        $user = SysUsrmUsers::where('username', $request->username)->first();
    
        if (!$user) {
            return response()->json(['status' => 1, 'errors' => ['User not found.']]);
        }
    
        // Prepare data for insertion
        $data = [];
        foreach ($request->role as $roleId) {
            $data[] = [
                'username' => $user->username,
                'role_id' => $roleId,
                'organisation_id' => $organisation->id,
                'created_by' => Auth::user()->name, // Assuming authenticated user
                'updated_by' => Auth::user()->name, // Same as created for now
            ];
        }
    
        // Insert multiple records into the database
        QmsRoleAsgns::insert($data);
    
        return response()->json(['status' => 2, 'message' => 'Roles assigned successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');
       // Total records count for pagination
        $totalRecords = QmsRoleAsgns::where('organisation_id', $organisation->id)->count(); // Count total records for the organisation

        // Query to get filtered records with grouping
        $query = QmsRoleAsgns::query()
            ->join('qms_role_rolls', 'qms_role_asgns.role_id', '=', 'qms_role_rolls.id')  // Join with qms_role_rolls
            ->join('sys_usrm_users', 'qms_role_asgns.username', '=', 'sys_usrm_users.username')  // Join with sys_usrm_users
            ->where('qms_role_asgns.organisation_id', $organisation->id); // Add organisation_id condition

        // Filter by role_name if provided
        if (!empty($roleName)) {
            $query->where('qms_role_rolls.role_name', 'LIKE', "%{$name}%");
        }

        // Group by username and aggregate roles
        $filteredRecords = $query->count(); // Get the count for filtered records before grouping

        // Fetch filtered results with pagination
        $rolesData = $query->select(
                'sys_usrm_users.username',
                'sys_usrm_users.name',
                'qms_role_asgns.created_by',
                'qms_role_asgns.updated_by',
                DB::raw('GROUP_CONCAT(qms_role_rolls.role_name SEPARATOR ", ") as role_names') // Aggregate role names
            )
            ->groupBy('sys_usrm_users.username', 'sys_usrm_users.name', 'qms_role_asgns.created_by', 'qms_role_asgns.updated_by')
            ->orderBy('sys_usrm_users.username') // You can change the order by as needed
            ->offset($start)
            ->limit($length)
            ->get();

        // Prepare data for response
        $rolesData = $rolesData->map(function ($item) {
            return [
                'username' => $item->username,
                'name' => $item->name,
                'role_name' => $item->role_names,
                'created_by' => $item->created_by,
                'updated_by' => $item->updated_by,
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
    public function edit(SysOrgaCtrls $organisation, Request $request)
    {
        $user_role = QmsRoleAsgns::where('username', $request->id)->pluck('role_id')->toArray();

        $roles = QmsRoleRolls::get();
        if(!$user_role){
            return response()->json(['status' => 1, 'message' => 'User not found']);
        }        
        $user = SysUsrmUsers::where('username', $request->id)
                ->first();

        return response()->json(['data' => view('qms.role.asgns.edit', compact('user','user_role', 'roles', 'organisation'))->render()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required|integer',
            'role' => 'required|array',
            'role.*' => 'required|integer', // Each role ID should be an integer
        ]);
        
        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }
        
        // Find the user by username
        $user = SysUsrmUsers::where('username', $request->username)
                    ->first();
        if (!$user) {
            return response()->json(['status' => 1, 'errors' => ['User not found.']]);
        }
        
        // Delete all existing role assignments for the user
        QmsRoleAsgns::where('username', $user->username)
                    ->where('organisation_id', $organisation->id)
                    ->delete();
        
        // Prepare data for insertion
        $data = [];
        foreach ($request->role as $roleId) {
            $data[] = [
                'username' => $user->username,
                'role_id' => $roleId,
                'organisation_id' => $organisation->id,
                'created_by' => Auth::user()->name, // Assuming authenticated user
                'updated_by' => Auth::user()->name, // Same as created for now
            ];
        }
        
        // Insert new roles into the database
        QmsRoleAsgns::insert($data);
        
        return response()->json(['status' => 2, 'message' => 'Roles updated successfully.']);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required|integer',
        ]);
        
        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }
        
        // Find the user by username
        $user = SysUsrmUsers::where('username', $request->username)
                    ->first();
        if (!$user) {
            return response()->json(['status' => 1, 'errors' => ['User not found.']]);
        }
        
        // Delete all existing role assignments for the user
        QmsRoleAsgns::where('username', $user->username)->delete();
        return response()->json(['status' => 2, 'message' => 'Roles removed successfully.']);        
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MainModule;
use App\Models\Role;
use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
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

        return view('rbp.role.index' , compact('organisation', 'mainModule'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation, Request $request)
    {
        $users = SysUsrmUsers::get();

        $role = new Role();

        $mainModule = SysModuMains::find($request->id);
        
        return response()->json(['data' => view('rbp.role.create', compact('organisation', 'users', 'role', 'mainModule'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {   
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'user' => 'sometimes',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $mainModule = SysModuMains::find($request->main_id);

        Role::create([
            'name' => $request->name,
            'user_id' => $request['user'],
            'main_module' => $mainModule->id,
            'organisation_id' => $organisation->id,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'Role added successfully!']);
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
            $query = Role::where('organisation_id', $organisation->id);
        }else{
            // Initialize the query to fetch permissions belonging to the organization
            $query = Role::where('organisation_id', $organisation->id)->where('main_module', $request->main_id);
        }

        // Apply name filter if provided
        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        // Get total records
        $totalRecords = Role::where('organisation_id', $organisation->id)->where('main_module', $request->main_id)->count();

        // Get filtered records
        $filteredRecords = $query->count();

        // Fetch the paginated data
        $data = $query->orderBy('id', 'DESC')
            ->offset($start)
            ->limit($length)
            ->get();

        // Extract all unique role IDs from the fetched permissions
        $allUserIds = $data->pluck('user_id')->flatten()->unique()->filter()->values();

        // Fetch role names corresponding to the IDs
        $usersMap = [];
        if ($allUserIds->isNotEmpty()) {
            $usersMap = SysUsrmUsers::whereIn('id', $allUserIds)->pluck('name', 'id')->toArray();
        }
        // Map permissions data to include role names
        $formattedData = $data->map(function ($item) use ($usersMap) {
            // Decode the roles JSON array
            $userIds = is_array($item->user_id) ? $item->user_id : json_decode($item->user_id, true) ?? [];

            // Map role IDs to names
            $userNames = collect($userIds)
                ->map(function ($id) use ($usersMap) {
                    return $usersMap[$id] ?? 'Unknown';
                })
                ->filter() // Remove any 'Unknown' if desired
                ->values()
                ->all();

            // Convert the array of role names to a comma-separated string
            $usersString = implode(', ', $userNames);

            return [
                'id' => $item->id,
                'name' => $item->name,
                'user' => $usersString,
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
    public function edit(SysOrgaCtrls $organisation, Request $request)
    {
        $mainModule = MainModule::find($request->mainModule); // Replace with actual main module ID

        $role = Role::find($request->role_id);

        $users = SysUsrmUsers::get();

        $userCounter = 0;
        
        $selectedUsers = $role->users->pluck('id')->toArray() ?? [];

        return response()->json(['data' => view('rbp.perm.create', compact('organisation', 'mainModule', 'role', 'users'))->render()]);
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

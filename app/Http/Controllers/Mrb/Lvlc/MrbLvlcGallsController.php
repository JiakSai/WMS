<?php

namespace App\Http\Controllers\Mrb\Lvlc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmGrpcs;
use App\Models\Sys\Usrm\SysUsrmUsers;
use App\Models\Qms\Role\QmsRoleRolls;
use App\Models\Mrb\Lvlc\MrbLvlcGalls;
use App\Models\Mrb\Lvlc\MrbLvlcRolls;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class MrbLvlcGallsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('mrb.lvlc.galls.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    
    public function create(SysOrgaCtrls $organisation)
    {
        $level = MrbLvlcRolls::get();
        //Need to add which organisation already added afterwards
        // Step 1: Get all group IDs from SysUsrmGrpcs
        $existingGroupIds = MrbLvlcGalls::pluck('group_id')->toArray();

        // Step 2: Get all records from SysUsrmGrpcs where group_id is not in the existing group IDs
        $groups = SysUsrmGrpcs::whereNotIn('id', $existingGroupIds)->get();
        $organisationId = $organisation->id;
        $users = SysUsrmUsers::get(); // Specify the fields to retrieve

        // Check if the collection is empty
        if ($users->isEmpty()) {
            $userData = []; 
        } else {
            // Prepare the user data in the desired format
            $userData = $users->map(function ($item) {
                return [
                    'username' => $item->username,
                    'name' => $item->name,
                ];
            });           
        }
        return response()->json(['data' => view('mrb.lvlc.galls.create', compact('organisation', 'level', 'userData', 'groups'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, SysOrgaCtrls $organisation)
    {
        $validate = Validator::make($request->all(), [
            'group' => 'required',
            'userSelect_0' => 'required|array',
            'userSelect_1' => 'required|array',
            'userSelect_2' => 'required|array',
            'userSelect_3' => 'required|array',
            'userSelect_4' => 'required|array',
            'userSelect_5' => 'required|array',
            'userSelect_6' => 'required|array',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        // Loop through each `userSelect_X` field in the request
        foreach ($request->all() as $key => $usernames) {
            // Check if the key is a userSelect field
            if (str_starts_with($key, 'userSelect_') && is_array($usernames)) {
                // Extract the level number from the key
                $level = intval(str_replace('userSelect_', '', $key)) + 1;

                // Loop through each username in the current userSelect array
                foreach ($usernames as $username) {
                    // Create a new entry for each username with the correct group and level
                    MrbLvlcGalls::create([
                        'group_id' => $request->group,
                        'username' => $username,
                        'level_id' => $level,
                        'organisation_id' => $organisation->id,
                        'created_by' => Auth::user()->name,
                        'updated_by' => Auth::user()->name,
                    ]);
                }
            }
        }

        return response()->json(['status' => 2, 'message' => 'Group Created Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, SysOrgaCtrls $organisation)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $roleName = $request->input('name', '');  // Capture role_name input

        // Define the base query with raw SQL to group by and aggregate by levels
        $query = DB::table('mrb_lvlc_galls')
        ->selectRaw('
            g.group_id, 
            r.name,
            g.created_by, 
            g.updated_by,
            g.organisation_id,
            GROUP_CONCAT(CASE WHEN g.level_id = 1 THEN g.username END ORDER BY g.username SEPARATOR ", ") AS level_1,
            GROUP_CONCAT(CASE WHEN g.level_id = 2 THEN g.username END ORDER BY g.username SEPARATOR ", ") AS level_2,
            GROUP_CONCAT(CASE WHEN g.level_id = 3 THEN g.username END ORDER BY g.username SEPARATOR ", ") AS level_3,
            GROUP_CONCAT(CASE WHEN g.level_id = 4 THEN g.username END ORDER BY g.username SEPARATOR ", ") AS level_4,
            GROUP_CONCAT(CASE WHEN g.level_id = 5 THEN g.username END ORDER BY g.username SEPARATOR ", ") AS level_5,
            GROUP_CONCAT(CASE WHEN g.level_id = 6 THEN g.username END ORDER BY g.username SEPARATOR ", ") AS level_6,
            GROUP_CONCAT(CASE WHEN g.level_id = 7 THEN g.username END ORDER BY g.username SEPARATOR ", ") AS level_7
        ')
        ->from('mrb_lvlc_galls as g')
        ->join('sys_usrm_grpcs as r', 'g.group_id', '=', 'r.id') // Adjust the join condition as needed
        ->where('g.organisation_id', $organisation->id)
        ->groupBy('g.group_id', 'r.name', 'g.created_by', 'g.updated_by','g.organisation_id')
        ->orderBy('g.created_by');

        $totalRecords = $query->get()->count();
        // Apply the filter if `role_name` is provided
        if (!empty($roleName)) {
            $query->where('level', 'LIKE', "%{$roleName}%");
        }
        
        $filteredRecords = $query->get()->count();
        // Get the total and filtered records count
        
        

        // Fetch filtered results with pagination
        $rolesData = $query->orderBy('group_id')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                    
        
                              
        // Map the data for response
        $rolesData = $rolesData->map(function ($item) {
            $userCount = SysOrgaCtrls::with(['users' => function($query) use ($item) {
                $query->where('group', $item->group_id);
            }])
            ->where('id', $item->organisation_id)
            ->first()
            ->users
            ->count();
            

            return [
                'group_id' => $item->group_id,
                'group_name' => $item->name,
                'total_user' => $userCount,
                'created_by' => $item->created_by,
                'updated_by' => $item->updated_by,
                'level_1' => $item->level_1 ?? '',
                'level_2' => $item->level_2 ?? '',
                'level_3' => $item->level_3 ?? '',
                'level_4' => $item->level_4 ?? '',
                'level_5' => $item->level_5 ?? '',
                'level_6' => $item->level_6 ?? '',
                'level_7' => $item->level_7 ?? ''
            ];
        });

        // Prepare the JSON response
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
    public function edit(Request $request, SysOrgaCtrls $organisation)
    {

        $level = MrbLvlcRolls::get();
        $groups = SysUsrmGrpcs::find($request->id);
        $organisationId = $organisation->id;

        // Retrieve selected users by level for the specific group
        $selected_users_by_level = MrbLvlcGalls::where('group_id', $request->id)
            ->get()
            ->groupBy('level_id')
            ->map(function ($users) {
                return $users->pluck('username')->toArray();
            });

        // Retrieve all users
        $users = SysUsrmUsers::get();

        // Prepare user data
        $userData = $users->map(function ($item) {
            return [
                'username' => $item->username,
                'name' => $item->name,
            ];
        });

        return response()->json([
            'data' => view('mrb.lvlc.galls.edit', compact('organisation', 'level', 'userData', 'groups', 'selected_users_by_level'))->render()
        ]);
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'group_id' => 'required',
            'userSelect_0' => 'required|array',
            'userSelect_1' => 'required|array',
            'userSelect_2' => 'required|array',
            'userSelect_3' => 'required|array',
            'userSelect_4' => 'required|array',
            'userSelect_5' => 'required|array',
            'userSelect_6' => 'required|array',
        ]);
        
        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }
        
        // Delete all existing role assignments for the user
        MrbLvlcGalls::where('group_id', $request->group_id)
                    ->where('organisation_id', $organisation->id)
                    ->delete();
        
        foreach ($request->all() as $key => $usernames) {
            // Check if the key is a userSelect field
            if (str_starts_with($key, 'userSelect_') && is_array($usernames)) {
                // Extract the level number from the key
                $level = intval(str_replace('userSelect_', '', $key)) + 1;

                // Loop through each username in the current userSelect array
                foreach ($usernames as $username) {
                    // Create a new entry for each username with the correct group and level
                    MrbLvlcGalls::create([
                        'group_id' => $request->group_id,
                        'username' => $username,
                        'level_id' => $level,
                        'organisation_id' => $organisation->id,
                        'created_by' => Auth::user()->name,
                        'updated_by' => Auth::user()->name,
                    ]);
                }
            }
        }
        
        return response()->json(['status' => 2, 'message' => 'Groups updated successfully.']);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

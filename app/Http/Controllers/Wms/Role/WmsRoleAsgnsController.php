<?php

namespace App\Http\Controllers\Wms\Role;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sys\SysOrgaController;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmUsers;
use App\Models\Wms\Role\WmsRoleAsgns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WmsRoleAsgnsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('wms.role.asgns.index' , compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $users = SysUsrmUsers::get();

        return response()->json(['data' => view('wms.role.asgns.create', compact('organisation', 'users'))->render()]);
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

        WmsRoleAsgns::create([
            'name' => $request->name,
            'user_id' => $request['user'],
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

        // Initialize the query to fetch permissions belonging to the organization
        $query = WmsRoleAsgns::where('organisation_id', $organisation->id);

        // Apply name filter if provided
        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        // Get total records
        $totalRecords = WmsRoleAsgns::where('organisation_id', $organisation->id)->count();

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
    public function edit(string $id)
    {
        /
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

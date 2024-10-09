<?php

namespace App\Http\Controllers\Sys\Usrm;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmGrpcs;
use App\Models\Sys\Usrm\SysUsrmUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SysUsrmGrpcsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('sys.usrm.grpcs.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        return response()->json(['data' => view('sys.usrm.grpcs.create', compact('organisation'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' =>'required|unique:sys_usrm_grpcs,name',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $group = SysUsrmGrpcs::create([
            'name' => $request->name,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2 , 'message' => 'User Group added successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = SysUsrmGrpcs::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = SysUsrmGrpcs::count();
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
     * Display the child data.
     */
    public function showChildData(SysOrgaCtrls $organisation, Request $request)
    {
        $parentId = $request->parentId;

        $parentRecord = SysUsrmGrpcs::where('id', $parentId)->first();

        if(!$parentRecord){
            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
        }

        $childData = SysUsrmUsers::where('group', $parentRecord->id)
                                 ->where('is_active', 1)
                                 ->get();

        $formattedChildData = $childData->map(function ($item) {
            return [
                'id' => $item->id,
                'username' => $item->username,
                'name' => $item->name,
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $childData->count(),
            "recordsFiltered" => $childData->count(),
            "data" => $formattedChildData
        ];

        return response()->json($json_data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SysOrgaCtrls $organisation, Request $request)
    {
        $group = SysUsrmGrpcs::find($request->id);

        if(!$group){
            return response()->json(['status' => 1, 'message' => 'User Group not found']);
        }

        return response()->json(['data' => view('sys.usrm.grpcs.edit', compact('group', 'organisation'))->render()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:sys_usrm_grpcs,name,'. $request->id
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }
        
        $group = SysUsrmGrpcs::find($request->id);

        if(!$group){
            return response()->json(['status' => 1, 'message' => 'User group not found']);
        }

        $group->update([
            'name' => $request->name,
            // 'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'User group updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $group = SysUsrmGrpcs::find($request->id);

        if(!$group){
            return response()->json(['status' => 1, 'message' => 'Organisation not found']);
        }

        $group->delete();

        return response()->json(['status' => 2, 'message' => 'Organisation deleted successfully']);
    }

    /**
    * Show the form that add user to the group.
    */
    public function addUser(SysOrgaCtrls $organisation, Request $request)
    {
        $group = SysUsrmGrpcs::where('id', $request->id)->first();

        if(!$group){
            return response()->json(['status' => 1, 'message' => 'Group not found']);
        }

        $usersNotInGroup = SysUsrmUsers::where('group', 0)->get();

        $data = view('sys.usrm.grpcs.add-user', compact('group', 'organisation', 'usersNotInGroup'))->render();

        return response()->json(['status' => 2,'data' => $data]);
    }

    /**
    * Store the user to the group
    */
    public function storeUser(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required',
            'users' => 'required|array',
            'users.*' => 'exists:users,id', // Ensure each user ID exists in the users table
        ]);
    
        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }
    
        foreach($request->users as $user){
            $user = SysUsrmUsers::find($user);
            if(!$user){
                return response()->json(['status'=> 1, 'message'=> 'User Not Found']);
            }
    
            $user->update([
                'group' => $request->id
            ]);
        }
    
        return response()->json(['status'=> 2, 'message'=> 'User(s) added to group successfully!']);
    }

    /**
     * Remove the user from the group
     */
    public function destroyUser(SysOrgaCtrls $organisation, Request $request)
    {
        $user = SysUsrmUsers::find($request->id);

        if(!$user){
            return response()->json([ 'status' => 1, 'message' => 'User not found',]);
        }

        $user->update([
            'group' => 0
        ]);

        return response()->json(['status' => 2, 'message'=> 'Remove user successfully!']);
    }
}

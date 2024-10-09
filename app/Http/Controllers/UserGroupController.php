<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserGroupController extends Controller
{
    public function index(Warehouse $warehouse){
        return view('user.group', compact('warehouse'));
    }

    public function getGroupData(Warehouse $warehouse, Request $request){

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = UserGroup::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = UserGroup::count();

        $filteredRecords = $query->count();

        $group = $query->where('is_deleted', 0)
                    ->orderBy('id', 'DESC')
                    ->offset($start)
                    ->limit($length)
                    ->get();

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $group
        ];

        return response()->json($json_data);
    }

    public function getGroupChildData(Warehouse $warehouse, Request $request){
    
        $parentId = $request->parentId;

        $parentRecord = UserGroup::where('id', $parentId)->first();

        if(!$parentRecord){
            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
        }

        $childData = User::where('group', $parentRecord->id)
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

    public function addGroup(Warehouse $warehouse, Request $request){
        $name = '';

        $data = view('user.addGroup', compact('name', 'warehouse'))->render();

        return response()->json(['data' => $data]);
    }

    public function editGroup(Warehouse $warehouse, Request $request){

        $group = UserGroup::where('id' , $request->id)->first();

        if(!$group){
            return response()->json(['status' => 1, 'message' => 'Group not found']);
        }

        $id = $group->id;
        $name = $group->name;
        
        $data = view('user.editGroup', compact('id', 'name', 'warehouse'))->render();

        return response()->json(['status'=> 2 ,'data' => $data]);

    }

    public function addGroupUser(Warehouse $warehouse, Request $request){

        $group = UserGroup::where('id', $request->id)->first();

        if(!$group){
            return response()->json(['status' => 1, 'message' => 'Group not found']);
        }

        $usersNotInGroup = User::where('group', 0)->get();

        $data = view('user.addToGroup', compact('group', 'warehouse', 'usersNotInGroup'))->render();

        return response()->json(['data' => $data]);
    }

    public function addGroupUserSubmit(Warehouse $warehouse, Request $request){

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
            $user = User::find($user);
            if(!$user){
                return response()->json(['status'=> 1, 'message'=> 'User Not Found']);
            }
    
            $user->update([
                'group' => $request->id
            ]);
        }
    
        return response()->json(['status'=> 2, 'message'=> 'User(s) added to group successfully!']);
    }

    public function addGroupSubmit(Warehouse $warehouse, Request $request){

        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $group = UserGroup::create([
                'name' => $request->name,
                'warehouse_id' => $warehouse->id,
                'created_by' => Auth::user()->name,
                'updated_by' => Auth::user()->name,
            ]);

        return response()->json(['status' => 2, 'message' => 'Group added successfully']);
    }

    public function editGroupSubmit(Warehouse $warehouse, Request $request){

        $validate = Validator::make($request->all(), [
            'id' => 'required|exists:user_groups,id',
            'name'=> 'required',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1,'errors'=> $errors]);
        }

        $group = UserGroup::where('id', $request->id)->first();

        if(!$group){
            return response()->json(['status' => 1, 'message' => 'Group not found']);
        }

        $group->update([
            'name'=> $request->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status'=> 2, 'message'=> 'Group update successfully']);

    }

    public function deleteGroup(Warehouse $warehouse, Request $request){

        $group = UserGroup::where('id', $request->id)->first();

        if(!$group){
            return response()->json(['status' => 1, 'message' => 'Group not found']);
        }

        $group->update([
            'is_deleted' => 1
        ]);

        return response()->json(['status' => 2, 'message' => 'Group deleted successfully']);
    }

    public function removeGroupUser(Warehouse $warehouse, Request $request){
    
        $user = User::find($request->userId);

        if(!$user){
            return response()->json([ 'status' => 1, 'message' => 'User not found',]);
        }

        $user->update([
            'group' => 0
        ]);

        return response()->json(['status' => 2, 'message'=> 'Remove user successfully!']);

    }
}

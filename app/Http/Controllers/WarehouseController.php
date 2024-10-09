<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WarehouseController extends Controller
{
    public function index(){
        return view('warehouseControl');
    }

    public function getWarehouseData(Request $request){

        $start = $request->input('start', 0);
        $length = $request->input('', 10);
        $warehouseName = $request->input('name', '');

        $query = Warehouse::query();

        if(!empty($warehouseName)){
            $query->where('Name', 'LIKE', "%{$warehouseName}%");
        }

        $totalRecords = Warehouse::count();
        $filteredRecords = $query->count();

        $data = $query->where('is_deleted', 0)
                      ->orderBy('id', 'DESC')
                      ->offset($start)
                      ->limit($length)
                      ->get();

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ];
        
        return response()->json($json_data);
    }

    public function getChildData(Request $request){
        
        $parentId = $request->input('parentId');

        $childData = Warehouse::with('users')
                          ->where('id', $parentId)
                          ->first()
                          ->users;

        return response()->json(['data' => $childData], 200);
    }

    public function addWarehouse(Request $request){
        $name = '';
        $created_by = Auth::user()->name;
        $updated_by = Auth::user()->name;
        $id = '';

        $data = view('addWarehouse', compact('name', 'created_by', 'updated_by', 'id'))->render();

        return response()->json(['data' => $data]);
    }

    public function editWarehouse(Request $request){

        $warehouse = Warehouse::where('id', $request->id)->first();

        if(!$warehouse){
            return response()->json(['status' => 1, 'message' => 'Warehouse not found']);
        }

        $name = $warehouse->name;
        $id = $request->id;
        $created_by = $warehouse->created_by;
        $updated_by = Auth::user()->name;

        $data = view('editWarehouse', compact('name', 'created_by', 'updated_by', 'id'))->render();

        return response()->json(['data' => $data]);
    }


    public function getNotWarehouseUser(Request $request){
        
        $warehouse = Warehouse::where('id', $request->id)->first();

        if (!$warehouse) {
            return response()->json(['status' => 1, 'message' => 'Warehouse not found']);
        }

        // Get all users who are not in the warehouse
        $usersNotInWarehouse = DB::table('users')
        ->leftJoin('user_warehouse', function($join) use ($request) {
            $join->on('users.id', '=', 'user_warehouse.user_id')
                ->where('user_warehouse.warehouse_id', '=', $request->id);
        })
        ->whereNull('user_warehouse.user_id')
        ->get();

        $name = $warehouse->name;
        $id = $request->id;
        $created_by = $warehouse->created_by;

        $data = view('addUserToWarehouse', compact('name', 'created_by', 'usersNotInWarehouse', 'id'))->render();
        return response()->json(['data' => $data]);
        // $name = $warehouse->name;
        // $id = $request->id;
        // $created_by = $warehouse->created_by;
        // $updated_by = Auth::user()->name;

        // // Render the view with the additional data
        // $data = view('editWarehouse', compact('name', 'created_by', 'updated_by', 'id', 'usersNotInWarehouse'))->render();

        // return response()->json(['data' => $data]);
    }
    
    public function addWarehouseSubmit(Request $request){
       
        $validate = Validator::make($request->all(), [
            'name' =>'required|unique:warehouses,name',
            'created_by' => 'required',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'created_by' => $request->created_by,
            'updated_by' => $request->created_by,
        ]);

        $message = 'Warehouse added successfully!';

        Log::channel('action_warehouse')->info(json_encode([
            'action' => 'create',
            'user_id' => Auth::user()->id,
            'data' => $warehouse->toArray()
        ]));

        return response()->json(['status' => 2 , 'message' => $message]);
    }
    public function addUserToWarehouseSubmit(Request $request){
        // Validate the request

        //dd($request->all());
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'users' => 'required|array',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }
    
        // Find the warehouse
        $warehouse = Warehouse::where('name', $request->name)->firstOrFail();
    
        $timestamp = Carbon::now()->format('Y-m-d H:i:s.u');
    
        // Attach users to the warehouse with a timestamp
        foreach($request->users as $user){
            $warehouse->users()->attach($user, ['created_at' => $timestamp]);
        }
        
    
        $message = 'User added successfully!';
    
        Log::channel('action_warehouse')->info(json_encode([
            'action' => 'create',
            'user_id' => Auth::user()->id,
            'data' => $warehouse->toArray()
        ]));
    
        return response()->json(['status' => 2, 'message' => $message]);
    }

    public function editWarehouseSubmit(Request $request){

        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'updated_by' => 'required',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $warehouse = Warehouse::where('id', $request->id)->first();
        $old_data = $warehouse->toArray();

        $warehouse->update([
            'name' => $request->name,
            'updated_by' => $request->updated_by,
        ]);

        $message = 'User updated successfully!';

        Log::channel('action_warehouse')->info(json_encode([
            'action' => 'update',
            'user_id' => Auth::user()->username,
            'old_data' => $old_data,
            'new_data' => $warehouse->toArray()
        ]));

        return response()->json(['status' => 2, 'message' => $message]);
    }

    public function remove(Request $request){

        $warehouse = Warehouse::where('id', $request->id)->first();

        if(!$warehouse){
            return response()->json([ 'status' => 1, 'message' => 'Warehouse not found',]);
        }

        $warehouse->update([
            'is_deleted' => 1,
        ]);

        Log::channel('action_warehouse')->info(json_encode([
            'action' => 'delete',
            'user_id' => Auth::user()->username,
            'data' => $warehouse->toArray()
        ]));

        return response()->json(['status' => 2, 'message' => 'Warehouse deleted successfully!']);
    }

    public function removeWarehouseUser(Request $request){

        $user = User::find($request->userId);

        $warehouse = Warehouse::find($request->warehouseId);

        if(!$user && !$warehouse){
            return response()->json([ 'status' => 1, 'message' => 'Data not found',]);
        }

        if($warehouse->id == $user->default_warehouse){
            return response()->json([ 'status'=> 1, 'message'=> 'Cannot remove user default warehouse!']);
        }

        $user->warehouses()->detach($warehouse->id);

        return response()->json(['status' => 2, 'message'=> 'Remove user successfully!']);

    }
}

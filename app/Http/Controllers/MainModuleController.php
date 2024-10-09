<?php

namespace App\Http\Controllers;

use App\Models\MainModule;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class MainModuleController extends Controller
{
    public function index(Warehouse $warehouse){
        return view('module.index', compact('warehouse'));
    }

    public function mainControl(Warehouse $warehouse){
        return view('module.mainControl', compact('warehouse'));
    }

    public function getMainModuleData(Warehouse $warehouse, Request $request){

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = MainModule::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = MainModule::count();
        $filteredRecords = $query->count();

        $mainModule = $query->where('is_deleted', 0)
                        ->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $mainModule = $mainModule->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'created_by' => $item->created_by,
                    'updated_by' => $item->updated_by,
                    'is_active' => $item->is_active ? 'Active' : 'Inactive',
                ];
            }); 

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $mainModule
        ];

        return response()->json($json_data);
    }

    public function addMainModule(Warehouse $warehouse, Request $request){

        $data = view('module.addMainModule', compact('warehouse'))->render();

        return response()->json(['data' => $data]);

    }

    public function editMainModule(Warehouse $warehouse, Request $request){
        
        $mainModule = MainModule::where('id', $request->id)->first();

        if(!$mainModule){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $name = $mainModule->name;
        $icon = $mainModule->icon;
        $id = $mainModule->id;

        $data = view('module.editMainModule', compact('name', 'icon', 'id', 'warehouse'))->render();

        return response()->json(['status'=> 2 ,'data' => $data]);
    }

    public function addMainModuleSubmit(Warehouse $warehouse, Request $request){

        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:modules,name',
            'code' => 'required|string|size:3|unique:main_modules,code',
            'icon' => 'required',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $mainModule = MainModule::create([

            'name' => $request->name,
            'icon' => $request->icon,
            'code' => $request->code,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,

        ]);

        return response()->json(['status' => 2, 'message' => 'Module added successfully']);
    }   

    public function editMainModuleSubmit(Warehouse $warehouse, Request $request){

        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:main_modules,name,' . $request->id,
            'icon' => 'required', 
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $mainModule = MainModule::where('id', $request->id)->first();
        $old_data = $mainModule->toArray();

        if(!$mainModule){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $mainModule->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'Module updated successfully']);

    }

    public function remove(Warehouse $warehouse, Request $request){

        $mainModule = MainModule::where('id', $request->id)->first();

        if(!$mainModule){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $mainModule->update([
            'is_deleted' => 1
        ]);

        return response()->json(['status' => 2, 'message' => 'Module deleted successfully']);
    }

    public function activate(Warehouse $warehouse, Request $request){

        $mainModule = MainModule::where('id', $request->id)->first();

        if(!$mainModule){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $mainModule->update([
            'is_active' => 1
        ]);

        return response()->json(['status' => 2, 'message' => 'Module successfully activated !']);
    }

    public function deactivate(Warehouse $warehouse, Request $request){

        $mainModule = MainModule::where('id', $request->id)->first();

        if(!$mainModule){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $mainModule->update([
            'is_active' => 0
        ]);

        return response()->json(['status' => 2, 'message' => 'Module successfully deactivated !']);
    }
}


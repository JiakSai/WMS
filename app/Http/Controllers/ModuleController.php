<?php

namespace App\Http\Controllers;

use App\Models\MainModule;
use App\Models\Module;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ModuleController extends Controller
{
    public function subControl(Warehouse $warehouse){
        return view('module.subControl', compact('warehouse'));
    }

    public function getModuleData(Warehouse $warehouse, Request $request){

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = Module::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = Module::count();
        $filteredRecords = $query->count();

        $module = $query->where('is_deleted', 0)
                        ->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $module = $module->map(function ($item) {
            $moduleGroup = MainModule::where('id', $item->group)->first();
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'group' => $moduleGroup->name,
                    'description' => $item->description,
                    'route' => $item->route,
                    'created_by' => $item->created_by,
                    'updated_by' => $item->updated_by,
                    'is_active' => $item->is_active ? 'Active' : 'Inactive',
                ];
            }); 

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $module
        ];

        return response()->json($json_data);
    }

    public function getPath(Warehouse $warehouse, Request $request){
    
        $subModule = Module::where('id', $request->id)->first();

        if(!$subModule){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        // Format the code to PascalCase
        $formattedCode = Str::studly(Str::singular($subModule->code));

        // Get the relative paths
        $modelPath = "app/Models/{$formattedCode}.php";
        $controllerPath = "app/Http/Controllers/{$formattedCode}Controller.php";
        
        // Find the migration file
        $migrationFiles = File::glob(database_path("migrations/*_create_*_table.php"));
        $migrationPath = null;
        foreach ($migrationFiles as $file) {
            if (strpos($file, $subModule->code) !== false) {
                $migrationPath = str_replace(base_path() . '/', '', $file);
                break;
            }
        }

        // Render the view with the paths
        $data = view('module.path', compact('formattedCode', 'controllerPath', 'migrationPath', 'modelPath'))->render();
    
        return response()->json(['status'=> 1, 'data'=> $data]);
    }

    public function addModule(Warehouse $warehouse, Request $request){

        $groups = MainModule::where('is_deleted', 0)->select('name','code', 'id')->get();

        $data = view('module.addSubModule', compact('warehouse', 'groups'))->render();

        return response()->json(['data' => $data]);
    }

    public function editModule(Warehouse $warehouse, Request $request){
        
        $module = Module::where('id', $request->id)->first();

        if(!$module){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $name = $module->name;
        $description = $module->description;
        $route = $module->route;
        $checked = $module->mobile === 1 ? 'checked' : '';
        $icon = $module->icon;
        $id = $module->id;

        $groups = MainModule::where('is_deleted', 0)->select('name', 'id')->get();
        $selectedGroup = $module->group;


        return response()->json(['status'=> 2 ,'data' => $data]);
    }

    public function addModuleSubmit(Warehouse $warehouse, Request $request){

        $validate = Validator::make($request->all(), [
            'group' => 'required|exists:main_modules,id',
            'name' => 'required|unique:modules,name',
            'code' => 'required|string|size:4|unique:modules,code',
            'description' => 'required',
            'route' => 'required',
            'icon' => 'required',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $mainModule = MainModule::where('id', $request->group)->first();

        $code = $mainModule->code . '_' . $request->code. 's';
        $formattedCode = Str::studly(Str::singular($code)); 

        $module = Module::create([
                'name' => $request->name,
                'code' => $code,
                'description' => $request->description,
                'route' => $request->route,
                'mobile' => $request->mobile ==='on' ? 1 : 0,
                'icon' => $request->icon,
                'group' => $request->group,
                'created_by' => Auth::user()->name,
                'updated_by' => Auth::user()->name,
            ]);

        Artisan::call('make:model ' . $formattedCode . ' -mcr');

        // Get the paths
        $modelPath = "app/Models/{$formattedCode}.php";
        $controllerPath = "app/Http/Controllers/{$formattedCode}Controller.php";
        $migrationFiles = File::glob(database_path("migrations/*_create_*_table.php"));
        $migrationPath = null;
        foreach ($migrationFiles as $file) {
            if (strpos($file, $code) !== false) {
                $migrationPath = str_replace(base_path() . '/', '', $file);
                break;
            }
        }

        Log::channel('action_module')->info(json_encode([
            'action' => 'create',
            'user_id' => Auth::user()->username,
            'data' => $module->toArray()
        ]));

        return response()->json([
            'status' => 2,
            'message' => 'Module added successfully',
            'controllerName' => "{$formattedCode}Controller",
            'controllerPath' => $controllerPath,
            'migrationName' => $migrationPath ? basename($migrationPath) : null,
            'migrationPath' => $migrationPath,
            'modelName' => $formattedCode,
            'modelPath' => $modelPath,
        ]);
    }   

    public function editModuleSubmit(Warehouse $warehouse, Request $request){

        $validate = Validator::make($request->all(), [
            'group' => 'required|exists:main_modules,id',
            'name' => 'required|unique:modules,name,' . $request->id,
            'description' => 'required',
            'route' => 'required',
            'icon' => 'required', 
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $module = Module::where('id', $request->id)->first();
        $old_data = $module->toArray();

        if(!$module){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $module->update([
            'name' => $request->name,
            'description' => $request->description,
            'mobile' => $request->mobile === 'on' ? 1 : 0,
            'route' => $request->route,
            'icon' => $request->icon,
            'group' => $request->group,
            'updated_by' => Auth::user()->name,
        ]);

        Log::channel('action_module')->info(json_encode([
            'action' => 'update',
            'user_id' => Auth::user()->username,
            'old_data' => $old_data,
            'new_data' => $module->toArray()
        ]));

        return response()->json(['status' => 2, 'message' => 'Module updated successfully']);

    }

    public function remove(Warehouse $warehouse, Request $request){

        $module = Module::where('id', $request->id)->first();

        if(!$module){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $module->update([
            'is_deleted' => 1
        ]);

        Log::channel('action_module')->info(json_encode([
            'action' => 'delete',
            'user_id' => Auth::user()->username,
            'data' => $module->toArray()
        ]));

        return response()->json(['status' => 2, 'message' => 'Module deleted successfully']);
    }

    public function activate(Warehouse $warehouse, Request $request){

        $module = Module::where('id', $request->id)->first();

        if(!$module){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $module->update([
            'is_active' => 1
        ]);

        Log::channel('action_module')->info(json_encode([
            'action' => 'activate',
            // 'user_id' => Auth::user()->Username,
            'data' => $module->toArray()
        ]));

        return response()->json(['status' => 2, 'message' => 'Module successfully activated !']);
    }

    public function deactivate(Warehouse $warehouse, Request $request){

        $module = Module::where('id', $request->id)->first();

        if(!$module){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $module->update([
            'is_active' => 0
        ]);

        Log::channel('action_module')->info(json_encode([
            'action' => 'deactivate',
            // 'user_id' => Auth::user()->Username,
            'data' => $module->toArray()
        ]));

        return response()->json(['status' => 2, 'message' => 'Module successfully deactivated !']);
    }

}

<?php

namespace App\Http\Controllers\Sys\Modu;

use App\Http\Controllers\Controller;
use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Modu\SysModuSubms;
use App\Models\Sys\Modu\SysModuTabms;
use App\Models\Sys\Orga\SysOrgaCtrls;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SysModuTabmsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('sys.modu.tabms.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $mainGroups = SysModuMains::select('id', 'name')->get();
        $subGroups = SysModuSubms::select('id', 'name', 'group', 'code')->get();

        return response()->json(['data' => view('sys.modu.tabms.create', compact('mainGroups', 'subGroups', 'organisation'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {   
        $validate = Validator::make($request->all(), [
            'mainGroup' => 'required|exists:sys_modu_mains,id',
            'subGroup' => 'required|exists:sys_modu_subms,id',
            'name' => 'required',
            'route' => 'required',
            'code' => 'required|string|unique:sys_modu_tabms,code'
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $groupCode = SysModuMains::select('code')->where('id', $request->mainGroup)->first()->code;
        $formattedGroupCode = Str::studly($groupCode);
    
        $subGroupCode = SysModuSubms::select('code')->where('id', $request->subGroup)->first()->code;
        $formattedSubGroupCode = Str::studly(preg_replace('/^' . preg_quote($groupCode, '/') . '_/', '', $subGroupCode));

        $tabCode = strtolower($request->code);
        $formattedTabCode = Str::studly(preg_replace('/^' . preg_quote($subGroupCode, '/') . '_/', '', $tabCode));


        $controllerName = Str::studly($tabCode) . 'Controller';
        $modelName = Str::studly($tabCode);

        $timestamp = date('Y_m_d_His');
        $migrationName = "{$timestamp}_create_{$tabCode}_table";

        $tab = SysModuTabms::create([
            'name' => $request->name,
            'main_group' => $request->mainGroup,
            'sub_group' => $request->subGroup,
            'route' => $request->route,
            'code' => $tabCode,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        Artisan::call('make:view', [
            'name' => strtolower("{$formattedGroupCode}/{$formattedSubGroupCode}/{$formattedTabCode}/index"),
        ]);
        $viewOutput = Artisan::output();
        // Extract the view path from the output
        preg_match('/View \[(.*)\] created successfully/', $viewOutput, $viewMatches);
        $viewPath = $viewMatches[1] ?? 'View path not found';

        Artisan::call('make:controller', [
            'name' => "{$formattedGroupCode}/{$formattedSubGroupCode}/{$controllerName}",
            '--resource' => true,
        ]);
        $controllerOutput = Artisan::output();
        // Extract the controller path from the output
        preg_match('/Controller \[(.*)\] created successfully/', $controllerOutput, $controllerMatches);
        $controllerPath = $controllerMatches[1] ?? 'Controller path not found';

        Artisan::call('make:model', [
            'name' => "{$formattedGroupCode}/{$formattedSubGroupCode}/{$modelName}",
            '--migration' => true,
        ]);
        $modelOutput = Artisan::output();
        // Extract the model path from the output
        preg_match('/Model \[(.*)\] created successfully/', $modelOutput, $modelMatches);
        $modelPath = $modelMatches[1] ?? 'Model path not found';

        // Extract the migration path from the output
        preg_match('/Migration \[(.*)\] created successfully/', $modelOutput, $migrationMatches);
        $migrationPath = $migrationMatches[1] ?? 'Migration path not found';

        return response()->json([
            'status' => 2,
            'message' => 'Tab added successfully',
            'controllerName' => $controllerName,
            'controllerPath' => $controllerPath,
            'modelName' => $modelName,
            'modelPath' => $modelPath,
            'migrationName' => $migrationName,
            'migrationPath' => $migrationPath,
            'viewName' => 'index.blade.php',
            'viewPath' => $viewPath
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation,Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = SysModuTabms::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = SysModuTabms::count();
        $filteredRecords = $query->count();

        $module = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $module = $module->map(function ($item) {
            $mainGroup = SysModuMains::where('id', $item->main_group)->first();
            $subGroup = SysModuSubms::where('id', $item->sub_group)->first();
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
                    'main_group' => $mainGroup->name,
                    'sub_group' => $subGroup->name,
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

    /**
    * Show the mvc files relative path.
    */
    public function showPath(SysOrgaCtrls $organisation, Request $request)
    {
        // Retrieve the tab module using the tab code
        $tabModule = SysModuTabms::where('id', $request->id)->first();

        if (!$tabModule) {
            return response()->json(['status' => 1, 'message' => 'Tab Module not found']);
        }

        // Retrieve the main group and sub group codes
        $groupCode = SysModuMains::select('code')->where('id', $tabModule->main_group)->first()->code;
        $formattedGroupCode = Str::studly($groupCode);

        $subGroupCode = SysModuSubms::select('code')->where('id', $tabModule->sub_group)->first()->code;
        $formattedSubGroupCode = Str::studly(preg_replace('/^' . preg_quote($groupCode, '/') . '_/', '', $subGroupCode));

        // Format the tab code
        $tabCode = strtolower($tabModule->code);
        $formattedTabCode = Str::studly(preg_replace('/^' . preg_quote($subGroupCode, '/') . '_/', '', $tabCode));

        // Construct the names
        $modelName = Str::studly($tabCode);
        $controllerName = Str::studly($tabCode) . 'Controller';
       
        // Construct the relative paths
        $modelPath = ("app/Models/{$formattedGroupCode}/{$formattedSubGroupCode}/{$modelName}.php");
        $controllerPath = ("app/Http/Controllers/{$formattedGroupCode}/{$formattedSubGroupCode}/{$controllerName}.php");
        $viewPath = "resources/views/" . strtolower($formattedGroupCode) . "/" . strtolower($formattedSubGroupCode) . "/" . strtolower($formattedTabCode) . "/index.blade.php";

        // Find the migration file
        $migrationFiles = File::glob(database_path("migrations/*_create_*_table.php"));
        $migrationPath = null;
        $migrationName = null;
        foreach ($migrationFiles as $file) {
            if (strpos($file, $tabModule->code) !== false) {
                $migrationPath = str_replace(base_path() . '/', '', $file);
                $migrationName = basename($file);
                break;
            }
        }
        
        $data = view('sys.modu.tabms.show-path', compact('migrationName', 'migrationPath', 'modelName', 'modelPath', 'controllerName','controllerPath', 'viewPath'))->render();
        
        return response()->json(['status'=> 2, 'data'=> $data]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SysOrgaCtrls $organisation, Request $request)
    {  
        $tabModule = SysModuTabms::find($request->id);

        if(!$tabModule)
        {
            return response()->json(['status' => 1, 'message' => 'Tab Module not found']);
        }

        $selectedMainGroup = SysModuMains::find($tabModule->main_group);

        $selectedSubGroup = SysModuSubms::find($tabModule->sub_group);

        $data = view('sys.modu.tabms.edit', compact('tabModule', 'selectedMainGroup', 'selectedSubGroup', 'organisation'))->render();

        return response()->json(['status'=> 2 ,'data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'mainGroup' => 'required|exists:sys_modu_mains,id',
            'subGroup' => 'required|exists:sys_modu_subms,id',
            'name' => 'required',
            'route' => 'required',
            'code' => 'required|string|unique:sys_modu_tabms,code,' . $request->id,
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $tabModule = SysModuTabms::find($request->id);

        if(!$tabModule){
            return response()->json(['status' => 1, 'message' => 'Sub Module not found']);
        }

        SysModuTabms::where('id', $tabModule->id)->update([
            'name' => $request->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'Sub Module updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $tabModule = SysModuTabms::find($request->id);

        if (!$tabModule) {
            return response()->json(['status' => 1, 'message' => 'Tab Module not found']);
        }
    
        // Check if the tab module exists in the database
        if (Schema::hasTable($tabModule->code)) {
            return response()->json(['status' => 1, 'message' => 'Tab Module cannot be deleted as it exists in the database']);
        }

        // Retrieve the main group and sub group codes
        $groupCode = SysModuMains::select('code')->where('id', $tabModule->main_group)->first()->code;
        $formattedGroupCode = Str::studly($groupCode);

        $subGroupCode = SysModuSubms::select('code')->where('id', $tabModule->sub_group)->first()->code;
        $formattedSubGroupCode = Str::studly(preg_replace('/^' . preg_quote($groupCode, '/') . '_/', '', $subGroupCode));

        // Format the tab code
        $tabCode = strtolower($tabModule->code);
        
        $formattedTabCode = Str::studly(preg_replace('/^' . preg_quote($subGroupCode, '/') . '_/', '', $tabCode));

        // Construct the names
        $modelName = Str::studly($tabCode);
        $controllerName = Str::studly($tabCode) . 'Controller';
        
        $controllerPath = app_path("Http/Controllers/{$formattedGroupCode}/{$formattedSubGroupCode}/{$controllerName}.php");
        $modelPath = app_path("Models/{$formattedGroupCode}/{$formattedSubGroupCode}/{$modelName}.php");
        $viewPath = resource_path("/views/" . strtolower($formattedGroupCode) . "/" . strtolower($formattedSubGroupCode) . "/" . strtolower($formattedTabCode));
        $migrationPath = database_path("migrations/*_create_{$tabCode}_table.php");

        if(File::exists($viewPath))
        {
            File::deleteDirectory($viewPath);
        }

        if(File::exists($controllerPath))
        {
            File::delete($controllerPath);
        }

        if(File::exists($modelPath))
        {
            File::delete($modelPath);
        }

        foreach (glob($migrationPath) as $file) {
            File::delete($file);
        }
    
        // Delete the tab module from the database
        $tabModule->delete();
    
        return response()->json(['status' => 2, 'message' => 'Tab Module deleted successfully']);
    }
}

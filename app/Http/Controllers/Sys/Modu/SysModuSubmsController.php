<?php

namespace App\Http\Controllers\Sys\Modu;

use App\Http\Controllers\Controller;
use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Modu\SysModuSubms;
use App\Models\Sys\Modu\SysModuTabms;
use App\Models\Sys\Orga\SysOrgaCtrls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SysModuSubmsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('sys.modu.subms.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $groups = SysModuMains::select('name', 'id', 'code')->get();

        return response()->json(['data' => view('sys.modu.subms.create', compact('groups','organisation'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'group' => 'required|exists:sys_modu_mains,id',
            'name' => 'required|unique:sys_modu_subms,name',
            'code' => 'required|string|size:8|unique:sys_modu_subms,code',
            'description' => 'required',
            'route' => 'required',
            'icon' => 'required',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $groupCode = SysModuMains::where('id', $request->group)->first()->code;
        $formattedGroupCode = Str::studly($groupCode);
        
        $subGroupCode = strtolower($request->code);
        $formattedSubGroupCode = Str::studly(preg_replace('/^' . preg_quote($groupCode, '/') . '_/', '', $subGroupCode));

        $controllerName = Str::studly($subGroupCode) . 'Controller';

        $module = SysModuSubms::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'route' => $request->route,
            'mobile' => $request->mobile ==='on' ? 1 : 0,
            'icon' => $request->icon,
            'group' => $request->group,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        Artisan::call('make:view', [
            'name' => strtolower("{$formattedGroupCode}/{$formattedSubGroupCode}/index"),
        ]);
        $viewOutput = Artisan::output();
        // Extract the view path from the output
        preg_match('/View \[(.*)\] created successfully/', $viewOutput, $viewMatches);
        $viewPath = $viewMatches[1] ?? 'View path not found';

        // Artisan::call('make:controller', [
        //     'name' => "{$formattedGroupCode}/{$controllerName}",
        //     '--resource' => true,
        // ]);
        // $controllerOutput = Artisan::output();
        // // Extract the controller path from the output
        // preg_match('/Controller \[(.*)\] created successfully/', $controllerOutput, $controllerMatches);
        // $controllerPath = $controllerMatches[1] ?? 'Controller path not found';

        return response()->json([
            'status' => 2,
            'message' => 'Sub Module added successfully',
            'viewPath' => $viewPath
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = SysModuSubms::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = SysModuSubms::count();
        $filteredRecords = $query->count();

        $module = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $module = $module->map(function ($item) {
            $moduleGroup = SysModuMains::where('id', $item->group)->first();
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
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

    public function showPath(SysOrgaCtrls $organisation, Request $request)
    {
        $subModule = SysModuSubms::where('id', $request->id)->first();

        if(!$subModule)
        {
            return response()->json(['status' => 2, 'message' => 'Tab Module not found']);
        }

        $groupCode = SysModuMains::select('code')->where('id', $subModule->group)->first()->code;
        $formattedGroupCode = Str::studly($groupCode);

        $formattedSubGroupCode = Str::studly(preg_replace('/^' . preg_quote($groupCode, '/') . '_/', '', $subModule->code));

        // $controllerName = Str::studly($subModule->code) . 'Controller';

        // $controllerPath = ("app/Http/Controllers/{$formattedGroupCode}/{$controllerName}.php");
        $viewPath = "resources/views/" . strtolower($formattedGroupCode) . "/" . strtolower($formattedSubGroupCode) . "/index.blade.php";

        $data = view('sys.modu.subms.show-path', compact('viewPath'))->render();

        return response()->json(['status' => 2, 'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SysOrgaCtrls $organisation, Request $request)
    {
        // $module = SysModuSubms::where('id', $request->id)->first();

        // if(!$module){
        //     return response()->json(['status' => 1, 'message' => 'Module not found']);
        // }

        // $name = $module->name;
        // $description = $module->description;
        // $route = $module->route;
        // $checked = $module->mobile === 1 ? 'checked' : '';
        // $icon = $module->icon;
        // $id = $module->id;

        // $groups = SysModuMains::select('name', 'id')->get();
        // $selectedGroup = $module->group;

        // $data = view('sys.modu.sub.edit', compact('name', 'description', 'route', 'icon', 'checked', 'groups', 'selectedGroup','id', 'organisation'))->render();

        // return response()->json(['status'=> 2 ,'data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        // $validate = Validator::make($request->all(), [
        //     'group' => 'required|exists:main_modules,id',
        //     'name' => 'required|unique:modules,name,' . $request->id,
        //     'description' => 'required',
        //     'route' => 'required',
        //     'icon' => 'required', 
        // ]);

        // if($validate->fails()){
        //     $errors = $validate->errors()->all();
        //     return response()->json(['status'=> 1, 'errors' => $errors]);
        // }

        // $module = SysModuSubms::where('id', $request->id)->first();
        // $old_data = $module->toArray();

        // if(!$module){
        //     return response()->json(['status' => 1, 'message' => 'Module not found']);
        // }

        // $module->update([
        //     'name' => $request->name,
        //     'description' => $request->description,
        //     'mobile' => $request->mobile === 'on' ? 1 : 0,
        //     'route' => $request->route,
        //     'icon' => $request->icon,
        //     'group' => $request->group,
        //     'updated_by' => Auth::user()->name,
        // ]);

        // return response()->json(['status' => 2, 'message' => 'Module updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $subModule = SysModuSubms::find($request->id);

        if(!$subModule){
            return response()->json(['status' => 1, 'message' => 'Sub Main Module not found']);
        }

        $tabModules = SysModuTabms::where('sub_group', $subModule->id)->get();

        if ($tabModules->isNotEmpty()) {
            return response()->json(['status' => 1, 'message' => 'Sub Module is unable to delete due to associated tab modules']);
        }

        $groupCode = SysModuMains::select('code')->where('id', $subModule->group)->first()->code;
        $formattedGroupCode = Str::studly($groupCode);

        $formattedSubGroupCode = Str::studly(preg_replace('/^' . preg_quote($groupCode, '/') . '_/', '', $subModule->code));

        // $controllerName = Str::studly($subModule->code) . 'Controller';

        // $controllerPath = app_path("Http/Controllers/{$formattedGroupCode}/{$controllerName}.php");
        $controllerDirectory = app_path("Http/Controllers/{$formattedGroupCode}/{$formattedSubGroupCode}");
        $modelDirectory = app_path("Models/{$formattedGroupCode}/{$formattedSubGroupCode}");
        $viewPath = resource_path("views/" . strtolower($formattedGroupCode) . "/" . strtolower($formattedSubGroupCode));

        if(File::exists($viewPath))
        {
            File::deleteDirectory($viewPath);
        }

        if(File::exists($controllerDirectory))
        {
            File::deleteDirectory($controllerDirectory);
        }

        // if(File::exists($controllerPath))
        // {
        //     File::delete($controllerPath);
        // }

        if(File::exists($modelDirectory))
        {
            File::deleteDirectory($modelDirectory);
        }

        $subModule->delete();

        return response()->json(['status' => 2, 'message' => 'Sub Module deleted successfully']);
    }

    /**
    * Activate the sub module
    */ 
    public function activate(Request $request)
    {
        $subModule = SysModuSubms::find($request->id);

        if(!$subModule){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $subModule->update([
            'is_active' => 1
        ]);

        return response()->json(['status' => 2, 'message' => 'Module successfully activated !']);
    }

    /**
     * Deactivate the sub module
     */
    public function deactivate(Request $request)
    {
        $subModule = SysModuSubms::find($request->id);

        if(!$subModule){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $subModule->update([
            'is_active' => 0
        ]);

        return response()->json(['status' => 2, 'message' => 'Module successfully deactivated !']); 
    }
}

<?php

namespace App\Http\Controllers\Sys\Modu;

use App\Http\Controllers\Controller;
use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Modu\SysModuSubms;
use App\Models\Sys\Modu\SysModuTabms;
use App\Models\Sys\Orga\SysOrgaCtrls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SysModuMainsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('sys.modu.mains.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
       return response()->json(['data' => view('sys.modu.mains.create', compact('organisation'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:sys_modu_mains,name',
            'code' => 'required|string|size:3|unique:sys_modu_mains,code',
            'icon' => 'required',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $mainModule = SysModuMains::create([

            'name' => $request->name,
            'icon' => $request->icon,
            'code' => $request->code,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,

        ]);

        return response()->json(['status' => 2, 'message' => 'Main Module added successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = SysModuMains::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = SysModuMains::count();
        $filteredRecords = $query->count();

        $mainModule = $query->orderBy('id', 'DESC')
                            ->offset($start)
                            ->limit($length)
                            ->get();
                       
        $mainModule = $mainModule->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SysOrgaCtrls $organisation, Request $request)
    {
        // $mainModule = SysModuMains::where('id', $request->id)->first();

        // if(!$mainModule){
        //     return response()->json(['status' => 1, 'message' => 'Main Module not found']);
        // }

        // $name = $mainModule->name;
        // $icon = $mainModule->icon;
        // $id = $mainModule->id;

        // $data = view('sys.modu.main.edit', compact('name', 'icon', 'id', 'organisation'))->render();

        // return response()->json(['status'=> 2 ,'data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // $validate = Validator::make($request->all(), [
        //     'name' => 'required|unique:sys_modu_mains,name,' . $request->id,
        //     'icon' => 'required', 
        // ]);

        // if($validate->fails()){
        //     $errors = $validate->errors()->all();
        //     return response()->json(['status'=> 1, 'errors' => $errors]);
        // }

        // $mainModule = SysModuMains::where('id', $request->id)->first();
        // $old_data = $mainModule->toArray();

        // if(!$mainModule){
        //     return response()->json(['status' => 1, 'message' => 'Module not found']);
        // }

        // $mainModule->update([
        //     'name' => $request->name,
        //     'icon' => $request->icon,
        //     'updated_by' => Auth::user()->name,
        // ]);

        // return response()->json(['status' => 2, 'message' => 'Main Module updated successfully']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $mainModule = SysModuMains::find($request->id);

        if(!$mainModule){
            return response()->json(['status' => 1, 'message' => 'Main Module not found']);
        }

        $subModules = SysModuSubms::where('group', $mainModule->id)->get();

        $tabModules = SysModuTabms::where('main_group', $mainModule->id)->get();

        if ($subModules->isNotEmpty() || $tabModules->isNotEmpty()) {
            return response()->json(['status' => 1, 'message' => 'Main Module is unable to delete due to associated submodules or tab modules']);
        }

        $groupCode = SysModuMains::select('code')->where('id', $mainModule->id)->first()->code;
        $formattedGroupCode = Str::studly($groupCode);

        $controllerDirectory = app_path("Http/Controllers/{$formattedGroupCode}");
        $modelDirectory = app_path("Models/{$formattedGroupCode}");
        $viewPath = resource_path("views/" . strtolower($formattedGroupCode));

        if(File::exists($viewPath))
        {
            File::deleteDirectory($viewPath);
        }

        if(File::exists($controllerDirectory))
        {
            File::deleteDirectory($controllerDirectory);
        }

        if(File::exists($modelDirectory))
        {
            File::deleteDirectory($modelDirectory);
        }

        $mainModule->delete();

        return response()->json(['status' => 2, 'message' => 'Module deleted successfully']);
    }

    /**
    * Activate the main module
    */ 
    public function activate(SysOrgaCtrls $organisation, Request $request)
    {
        $mainModule = SysModuMains::find($request->id);

        if(!$mainModule){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $mainModule->update([
            'is_active' => 1
        ]);

        return response()->json(['status' => 2, 'message' => 'Module successfully activated !']);
    }

    /**
     * Deactivate the main module
     */
    public function deactivate(SysOrgaCtrls $organisation, Request $request)
    {
        $mainModule = SysModuMains::find($request->id);

        if(!$mainModule){
            return response()->json(['status' => 1, 'message' => 'Module not found']);
        }

        $mainModule->update([
            'is_active' => 0
        ]);

        return response()->json(['status' => 2, 'message' => 'Module successfully deactivated !']); 
    }
}

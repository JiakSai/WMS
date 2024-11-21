<?php

namespace App\Http\Controllers\Qms\Ipqa;

use App\Http\Controllers\Controller;
use App\Models\Qms\Ipqa\QmsIpqaTemps;
use App\Models\Sys\Orga\SysOrgaCtrls;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\Qms\Role\QmsRoleAsgns;
use Illuminate\Support\Str; class QmsIpqaTempsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        $checkQuery = QmsRoleAsgns::where('organisation_id', $organisation->id)
        ->where('username', Auth::user()->username)
        ->where('role_id', 5)
        ->count();

        if ($checkQuery == 0){
            return view('qms.ipqa.temps.noperm', compact('organisation'));
        }
        return view('qms.ipqa.temps.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        return response()->json(['data' => view('qms.ipqa.temps.create', compact('organisation'))->render()]);
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
{
    $validate = Validator::make($request->all(), [
        'name' => 'required',
        'categories' => 'required',
        'fileToUpload' => 'required|file|mimes:xlsx|max:2048',
    ]);

    if($validate->fails()){
        $errors = $validate->errors()->all();
        return response()->json(['status'=> 1, 'errors' => $errors]);
    }

    // Generate a 100-character random string for folder_key
    $folderKey = Str::random(100);

    // Get the uploaded file
    $file = $request->file('fileToUpload');

    // Define the filename
    $filename = $request->name .  '_template.' . $file->getClientOriginalExtension();

    // Define the folder path using the folder key
    $folderPath = 'public/excel/ipqa_template/' . $folderKey;

    // Store the file in the generated folder
    $filePath = $file->storeAs($folderPath, $filename);

    // Store the file details in the QmsIpqaTemps model
    $ipqaTemp = QmsIpqaTemps::create([
        'organisation_id' => $organisation->id,
        'created_by' => Auth::user()->name, // Assuming authenticated user
        'updated_by' => Auth::user()->name, // Same as created for now
        'date_upload' => now(),
        'categories' => $request->categories,
        'remark' => $request->remark,
        'version_name' => $request->name,
        'folder_key' => $folderKey,
        'file_name' => $filename,
        'status' => 'Active', // Or any default status you want
    ]);

    return response()->json(['status' => 2, 'message' => 'File Uploaded Successfully', 'filePath' => $filePath]);
}

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = QmsIpqaTemps::query();

        // if (!empty($name)) {
        //     $query->where('version_name', 'LIKE', "%{$name}%");
        // }

        $totalRecords = QmsIpqaTemps::count();
        $filteredRecords = $query->count();

        $mainModule = $query->where('organisation_id', $organisation->id)->orderBy('id', 'DESC')
                            ->offset($start)
                            ->limit($length)
                            ->get();
                       
        $mainModule = $mainModule->map(function ($item) {
                return [
                    'id' => $item->id,
                    'version_name' => $item->version_name,
                    'date_upload' => $item->date_upload,
                    'file_name' => $item->file_name,
                    'created_by' => $item->created_by,
                    'updated_by' => $item->updated_by,
                    'categories' => $item->categories,
                    'remark' => $item->remark,
                    'is_active' => $item->status,
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
    public function edit(string $id)
    {
        //
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
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $ipqaTemps = QmsIpqaTemps::find($request->id);

        if(!$ipqaTemps){
            return response()->json(['status' => 1, 'message' => 'IPQA Checksheet Template not found']);
        }

        //$subModules = QmsIpqaTemps::where('template_id', $mainModule->id)->get();


        // if ($subModules->isNotEmpty() || $tabModules->isNotEmpty()) {
        //     return response()->json(['status' => 1, 'message' => 'Main Module is unable to delete due to associated submodules or tab modules']);
        // }

        $folder_key = QmsIpqaTemps::select('folder_key')->where('id', $request->id)->first()->folder_key;


        $viewPath = storage_path("app/public/excel/ipqa_template/" . $folder_key);

        if (File::exists($viewPath)) {
            File::deleteDirectory($viewPath);
            $ipqaTemps->delete();
            return response()->json(['status' => 2, 'message' => 'File deleted successfully']);
        } else {
            return response()->json(['status' => 1, 'message' => 'File not found']);
        }

    }

    public function activate(SysOrgaCtrls $organisation, Request $request){

        $user = QmsIpqaTemps::where('id', $request->id)->first();

        if(!$user){
            return response()->json(['status' => 1, 'message' => 'Template not found']);
        }

        $user->update([
            'status' => "Active"
        ]);

        return response()->json(['status' => 2, 'message' => 'Template successfully activated !']);
    }

    public function deactivate(SysOrgaCtrls $organisation, Request $request){

        $user = QmsIpqaTemps::where('id', $request->id)->first();

        if(!$user){
            return response()->json(['status' => 1, 'message' => 'Template not found']);
        }

        $user->update([
            'status' => "Deactived"
        ]);

        return response()->json(['status' => 2, 'message' => 'Template successfully deactivated !']);
    }
}

<?php

namespace App\Http\Controllers\Qms\Ipqa;

use App\Http\Controllers\Controller;
use App\Models\Qms\Ipqa\QmsIpqaTemps;
use App\Models\Sys\Orga\SysOrgaCtrls;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class QmsIpqaTempsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
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
            'name' => 'required|unique:qms_ipqa_temps,version_name',
            'fileToUpload' => 'required|file|mimes:xlsx|max:2048',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        // Get the uploaded file
        $file = $request->file('fileToUpload');

        // Define the filename
        $filename = $request->name . '_' . time() . '_template.' . $file->getClientOriginalExtension();

        // Store the file in the storage path
        $filePath = $file->storeAs('public/excel/ipqa_template', $filename); 



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

        $mainModule = $query->orderBy('id', 'DESC')
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
                    'is_active' => $item->status ? 'Active' : 'Inactive',
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
    public function destroy(string $id)
    {
        //
    }
}

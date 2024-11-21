<?php

namespace App\Http\Controllers\Env\Tmsv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sys\Orga\SysOrgaCtrls;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EnvTmsvSinvsImport;
use App\Models\Env\Tmsv\EnvTmsvSinvs;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class EnvTmsvSinvsController extends Controller
{

    public function showImportForm(SysOrgaCtrls $organisation, Request $request)
    {
        return view('env.tmsv.sinvs.import'); // Create an import view for uploading the Excel file
    }

    public function import(SysOrgaCtrls $organisation, Request $request)
    {
        $allData = $request->all();
   
        // Optionally, log or dump the data to inspect it
        // Log::info('All request data:', $allData);  
        // dd($allData); 
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ], [
            'file.required' => 'Please upload an Excel file.',
            'file.mimes' => 'Only .xlsx and .xls files are allowed.',
        ]);
            
        try {
            // Store the uploaded file in the 'uploads' folder under the specified directory
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileExists = EnvTmsvSinvs::where('FileName', $fileName)->exists();
            
            if ($fileExists) {
                return response()->json([
                    'status' => 1,
                    'message' => 'The file "' . $fileName . '" has already been imported.'
                ], 200);
            }
            
            $filePath = '/var/www/storage/app/uploads/' . $fileName;            
            $file->move(storage_path('app/uploads'), $fileName);    
            Log::debug('Stored File Path: ' . $filePath); 
            Excel::import(new EnvTmsvSinvsImport($fileName, $organisation, Auth::user()->name), $filePath);

      
  
            return response()->json([
                'status' => 2,
                'message' => 'File has been imported successfully'
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('File import error: ' . $e->getMessage());
            Log::error('Import error: ' . $e->getMessage(), [
                'file' => $request->file('excel_file')->getClientOriginalName(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            // Return a JSON response with an error status and message
            return response()->json([
                'status' => 1,
                'message' => 'Error importing data: ' . $e->getMessage()

            ]);
        }
    }
    
    public function index(SysOrgaCtrls $organisation, Request $request)
    {
        return view('env.tmsv.sinvs.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation, Request $request)
    {
        return response()->json(['data' => view('env.tmsv.sinvs.create', compact('organisation'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display excel content
     */
    // public function show(SysOrgaCtrls $organisation, Request $request)
    // {
    //     // $fromDate = $request->input('from_date');
    //     // $toDate = $request->input('to_date');
    //     $start = $request->input('start', 0);
    //     $length = $request->input('length', 10);
    //     $PN = $request->input('PN', '');

    //     $query = EnvTmsvSinvs::query();

    //     if (!empty($PN)) {
    //         $query->where('PN', 'LIKE', "%{$PN}%");
    //     }
    //     if (!empty($fromDate)) {
    //         $query->whereDate('Complete_Date', '>=', Carbon::parse($fromDate)->startOfDay());
    //     }
    
    //     if (!empty($toDate)) {
    //         $query->whereDate('Complete_Date', '<=', Carbon::parse($toDate)->endOfDay());
    //     }

    //     $totalRecords = EnvTmsvSinvs::count();
    //     $filteredRecords = $query->count();

    //     $data = $query    
     
    //     ->orderBy('id', 'DESC')
    //                     ->offset($start)
    //                     ->limit($length)
    //                     ->get();
                       
    //     $data = $data->map(function ($item) {

    //         return [
    //             'id' => $item->id,
    //             'PN'=>$item->PN,
    //             'WO'=>$item->WO,
    //             'PO_NO'=>$item->PO_NO,
    //             'QTY'=>$item->QTY,
    //             'WD_To_JV_Price'=>$item->WD_To_JV_Price,
    //             'WD_To_JV_Total_Quotation'=>$item->WD_To_JV_Total_Quotation,
    //             'SumTotalQuotation'=>$item->SumTotalQuotation,
    //             'TransactionNo'=>$item->TransactionNo,
    //             'Complete_QTY'=>$item->Complete_QTY,
    //             'Complete_Date'=>$item->Complete_Date,
    //             'Location'=>$item->Location,
    //             'JV_To_SMTT_Price'=>$item->JV_To_SMTT_Price,
    //             'JV_To_SMTT_Total_Quotation'=>$item->JV_To_SMTT_Total_Quotation,
    //             'organisation_id'=>$item->organisation_id,
    //             'created_at' => Carbon::parse($item->created_at)->format('Y-m-d H:i:s'),
    //             'created_by'=>$item->created_by,
    //             'updated_by'=>$item->updated_by,

    //         ];

    //     }); 

    //     $json_data = [
    //         // "draw" => intval($request->input('draw')),
    //         "recordsTotal" => $totalRecords,
    //         "recordsFiltered" => $filteredRecords,
    //         "data" => $data
    //     ];

    //     return response()->json($json_data);
    // }

     /**
     * Display the uploaded excel file 
     */
 

     public function show(SysOrgaCtrls $organisation, Request $request)
     {
         $start = $request->input('start', 0);
         $length = $request->input('length', 10);
         $fileName = $request->input('FileName', '');
     
         // Start building the query
         $query = DB::table('wms.env_tmsv_sinvs')
             ->select('FileName', 'created_by',DB::raw('MAX(updated_at) AS latest_updated_at'))
             ->groupBy('FileName','created_by')
             ->orderByDesc(DB::raw('MAX(updated_at)'));
        $totalRecords = $query ->count();
         // Apply the file name filter if provided
         if (!empty($fileName)) {
             $query->where('FileName', 'LIKE', "%{$fileName}%");
         }
        
          $filteredRecords = $query->count();
     
     
         $data = $query->offset($start)
             ->limit($length)
             ->get();
     
         // Format the data for the response
         $data = $data->map(function ($item) {
             return [
                 'FileName' => $item->FileName,
                 'latest_updated_at' => Carbon::parse($item->latest_updated_at)->format('Y-m-d H:i:s'),
                 'created_by' => $item->created_by,
             ];
         });
     
         // Prepare the JSON response
         $json_data = [
             "recordsTotal" => $totalRecords,
             "recordsFiltered" => $filteredRecords,
             "data" => $data
         ];
     
         return response()->json($json_data);
     }

     public function detail(SysOrgaCtrls $organisation, Request $request)
     {
         return view('env.tmsv.sinvs.import'); // Create an import view for uploading the Excel file
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

<?php

namespace App\Http\Controllers\Env\Sinv;
require_once __DIR__ . '/../../../../../vendor/autoload.php';

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Env\Sinv\Query\ARCNDNQ;
use App\Http\Controllers\Env\Sinv\Function\Tel;
use App\Models\Sys\Orga\SysOrgaCtrls;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use phpseclib3\Net\FTP;


use Exception;

class EnvSinvCndnsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation, Request $request)
    {
        return view('env.sinv.cndns.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation, Request $request)
    {
        return response()->json(['data' => view('env.sinv.cndns.create', compact('organisation'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, SysOrgaCtrls $organisation)
{
    $fromDate = $request->input('from_date_cndn');
    $toDate = $request->input('to_date_cndn');
    $fromDate = Carbon::parse($fromDate)->format('d-m-Y H:i:s');
    $toDate = Carbon::parse($toDate)->format('d-m-Y H:i:s');
   
    try {
        // Execute the query with the parameters
        $dataSecond = ARCNDNQ::getCNDN($fromDate, $toDate);
        
        $modifiedData = [];
        $invoiceIDs = []; 

        foreach ($dataSecond as $record) {
            // Convert the object to an array if needed for easier modification
            $recordArray = (array) $record;

            $recordArray['BNPType'] = 'BRN';
            $recordArray['SSTNo'] = 'NA';
            $recordArray['MSICCode'] = '00000';

            if (empty($recordArray['TIN'])) {
                $recordArray['TIN'] = 'error';
                Log::error("Error: Customer {$recordArray['CusCode']} ({$recordArray['CusRegName']}) has an empty TIN");
            }
            $recordArray['Tel'] = empty($recordArray['Tel']) ? '999' : Tel::standardizePhoneNumber($recordArray['Tel']);
            $recordArray['StateCode'] = $recordArray['Country'] !== 'MYS' ? '17' : $recordArray['StateCode'] ;
            $modifiedData[] = $recordArray;
        }

        $invoiceIDs = array_unique(array_column($modifiedData, 'InvoiceID'));
        $totalCount = count($modifiedData); 
        
        return response()->json([
            'data' => $modifiedData,
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $totalCount,
            'invoiceIDs' => $invoiceIDs,
        ]);
    } catch (\Exception $e) {
        Log::error('SQL Error: ' . $e->getMessage(), [
            'query' => $dataSecond,
            // 'params' => $queryParams, // Log the parameters for debugging
        ]);
        Log::info('Query Params: ', ['fromDate' => $fromDate, 'toDate' => $toDate]);

        return response()->json(['status' => 1, 'error' => 'Unable to fetch data'], 500);
    }
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
    public function saveCSV(Request $request,SysOrgaCtrls $organisation)
    {
        try {
            // Log the received CSV data for debugging
            Log::info('Received CSV data:', ['data' => $request->input('csvData')]);
    
            // Get the CSV data from the request
            $csvData = $request->input('csvData');
    
            // Define the file path and name
            $fileName = 'ARCNDN_' . now()->format('Ymd_His') . '.csv';
            $filePath = storage_path("app/public/csv_files/{$fileName}");
    
            // Ensure the directory exists
            if (!file_exists(storage_path('app/public/csv_files'))) {
                mkdir(storage_path('app/public/csv_files'), 0775, true);
                Log::info("Directory created at: " . storage_path('app/public/csv_files'));
            }
    
            // Save the CSV data to the file
            file_put_contents($filePath, $csvData);
    
            Log::info("CSV file saved successfully at: " . $filePath);
    
            return response()->json(['status' => 2, 'message' => 'CSV file saved successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error saving CSV file: ' . $e->getMessage());
            return response()->json(['status' => 1, 'message' => 'Failed to save CSV file'], 500);
        }
    }
    public function saveFTP(Request $request, SysOrgaCtrls $organisation)
{
    try {
        // Log the start of the process
        Log::info('Starting CSV upload to SFTP', ['organisation' => $organisation->name]);

        // Get the CSV data from the request
        $csvData = $request->input('csvData');
        Log::info('CSV data received', ['csvDataLength' => strlen($csvData)]);  // Log the length of the CSV data to avoid sensitive information exposure

        // Generate the file name and file path
        $fileName = 'ARCNDN_' . now()->format('Ymd_His') . '.csv';
        $filePath = storage_path('app/' . $fileName);
        Log::info('Generated file path', ['filePath' => $filePath]);

        // Save the CSV data temporarily to the server
        file_put_contents($filePath, $csvData);
        Log::info('CSV data written to temporary file', ['fileName' => $fileName]);

        // Check if the file has been created successfully
        if (file_exists($filePath)) {
            // Log before attempting to upload to SFTP
            Log::info('Preparing to upload CSV file to SFTP', ['fileName' => $fileName]);

            // Define the SFTP path to '/Share/eInvoice/Import/TestEnv'
            // $sftpPath = 'TestEnv/ARCNDN/' . $fileName; // Upload to /Share/eInvoice/Import/TestEnv
            $sftpPath = 'ARCNDN/' . $fileName;
            // Upload the file to the SFTP server with the modified path
            $disk = Storage::disk('sftp');
            $uploaded = $disk->put($sftpPath, fopen($filePath, 'r'));

            if ($uploaded) {
                // Log successful upload
                Log::info('CSV file successfully uploaded to SFTP', ['sftpPath' => $sftpPath]);

                // Delete the local file after upload
                unlink($filePath);
                Log::info('Temporary local file deleted', ['fileName' => $fileName]);

                return response()->json(['status' => 2, 'message' => 'CSV uploaded to SFTP successfully.']);
            } else {
                // Log failure to upload
                throw new Exception("Failed to upload CSV to SFTP.");
            }
        } else {
            // Log failure if file does not exist
            throw new Exception("Temporary file was not created successfully.");
        }
    } catch (Exception $e) {
        // Log detailed error with exception
        Log::error('Error uploading CSV to SFTP: ' . $e->getMessage(), [
            'exception' => $e,
            'fileName' => isset($fileName) ? $fileName : 'N/A',  // Ensure fileName is logged even if the exception occurs before it's set
            'csvDataLength' => isset($csvData) ? strlen($csvData) : 'N/A', // Log length to track data size
            'organisation' => isset($organisation->name) ? $organisation->name : 'N/A',
        ]);
        return response()->json(['status' => 1, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}


}

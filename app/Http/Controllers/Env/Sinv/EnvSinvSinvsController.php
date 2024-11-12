<?php

namespace App\Http\Controllers\Env\Sinv;
require_once __DIR__ . '/../../../../../vendor/autoload.php';

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Env\Sinv\Query\SinvQ;
use App\Http\Controllers\Env\Sinv\Function\Tel;
use App\Models\Sys\Orga\SysOrgaCtrls;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use phpseclib3\Net\FTP;


use Exception;
class EnvSinvSinvsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation, Request $request)
    {
       
        return view('env.sinv.sinvs.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation, Request $request)
    {
        // $invoiceIDs = $request->input('invoiceIDs');
        // $request->validate([
        //     'invoiceIDs' => 'required|array',
        //     'invoiceIDs.*' => 'string'  
        // ]);
    
        // // Retrieve the array of invoice IDs
        // $invoiceIDs = $request->input('invoiceIDs');
        // Return the view with the necessary data
        // $invoiceIds = $request->input('invoice_ids');
        return response()->json(['data' => view('env.sinv.sinvs.create', compact('organisation'))->render()]);
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
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $fromDate = Carbon::parse($fromDate)->format('d-m-Y H:i:s');
    $toDate = Carbon::parse($toDate)->format('d-m-Y H:i:s');
   
    
//     Log::info('From Date: ' . $fromDate);
// Log::info('To Date: ' . $toDate);


    // $query = SinvQ::getTopRecords();
    // $query->whereBetween('ddd', [$fromDate, $toDate]);
    // $query = SinvQ::getTopRecords($fromDate, $toDate);
    // $queryParams = [$fromDate, $toDate];
    // Initialize an array to hold the query parameters
    // $queryParams = [];
    
       

   
    try {
        // Execute the query with the parameters
        $dataSecond = SinvQ::getTopRecords($fromDate, $toDate);
        
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

            // if (!in_array($recordArray['InvoiceID'], $invoiceIDs)) {
            //     $invoiceIDs[] = $recordArray['InvoiceID'];
            // }
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

        return response()->json(['error' => 'Unable to fetch data'], 500);
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
        $fileName = 'invoice_data_' . now()->format('Ymd_His') . '.csv';
        $filePath = storage_path("app/public/csv_files/{$fileName}");

        // Ensure the directory exists
        if (!file_exists(storage_path('app/public/csv_files'))) {
            mkdir(storage_path('app/public/csv_files'), 0775, true);
            Log::info("Directory created at: " . storage_path('app/public/csv_files'));
        }

        // Save the CSV data to the file
        file_put_contents($filePath, $csvData);

        Log::info("CSV file saved successfully at: " . $filePath);

        return response()->json(['message' => 'CSV file saved successfully'], 200);
    } catch (\Exception $e) {
        Log::error('Error saving CSV file: ' . $e->getMessage());
        return response()->json(['message' => 'Failed to save CSV file'], 500);
    }
}



// public function saveCsvToFtp(Request $request, $organisationId)
// {
//     // Retrieve CSV data from the request
//     $csvData = $request->input('csvData');

//     // Define the FTP disk (make sure this is set up in your config/filesystems.php)
//     $ftpDisk = 'ftp';  // Define your FTP disk

//     // Prepare the file to be uploaded
//     $fileName = 'csv_file_' . time() . '.csv';
//     $filePath = 'path/to/ftp/folder/' . $fileName;

//     // Save the CSV data to a file temporarily
//     $tempFile = tmpfile();
//     fwrite($tempFile, $csvData);
//     fseek($tempFile, 0);

//     // Upload the file to FTP
//     try {
//         // Use Laravel Storage to upload the file to the FTP server
//         Storage::disk($ftpDisk)->put($filePath, $tempFile);

//         // Close the temp file
//         fclose($tempFile);

//         return response()->json(['message' => 'CSV file uploaded successfully via FTP']);
//     } catch (\Exception $e) {
//         return response()->json(['message' => 'Error uploading CSV file via FTP', 'error' => $e->getMessage()]);
//     }
// }
public function saveFTP(Request $request, SysOrgaCtrls $organisation)
{
    try {
        // Log the start of the process
        Log::info('Starting CSV upload to SFTP', ['organisation' => $organisation->name]);

        // Get the CSV data from the request
        $csvData = $request->input('csvData');
        Log::info('CSV data received', ['csvDataLength' => strlen($csvData)]);  // Log the length of the CSV data to avoid sensitive information exposure

        // Generate the file name and file path
        $fileName = 'Invoice' . now()->format('Ymd_His') . '.csv';
        $filePath = storage_path('app/' . $fileName);
        Log::info('Generated file path', ['filePath' => $filePath]);

        // Save the CSV data temporarily to the server
        file_put_contents($filePath, $csvData);
        Log::info('CSV data written to temporary file', ['fileName' => $fileName]);

        // Check if the file has been created successfully
        if (file_exists($filePath)) {
            // Log before attempting to upload to SFTP
            Log::info('Preparing to upload CSV file to SFTP', ['fileName' => $fileName]);

            // Upload the file to the SFTP server
            $disk = Storage::disk('sftp');
            $uploaded = $disk->put($fileName, fopen($filePath, 'r'));

            if ($uploaded) {
                // Log successful upload
                Log::info('CSV file successfully uploaded to SFTP', ['fileName' => $fileName]);
                
                // Delete the local file after upload
                unlink($filePath);
                Log::info('Temporary local file deleted', ['fileName' => $fileName]);

                return response()->json(['message' => 'CSV uploaded to SFTP successfully.']);
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
        return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}
// public function saveFTP(Request $request, SysOrgaCtrls $organisation)
// {
//     try {
//         // Log the start of the process
//         Log::info('Starting CSV upload to SFTP', ['organisation' => $organisation->name]);

//         // Get the CSV data from the request
//         $csvData = $request->input('csvData');
//         Log::info('CSV data received', ['csvDataLength' => strlen($csvData)]);

//         // Generate the file name for the SFTP path
//         $fileName = 'Invoice' . now()->format('Ymd_His') . '.csv';
//         Log::info('Generated file name', ['fileName' => $fileName]);

//         // Upload the file to the SFTP server directly
//         $disk = Storage::disk('sftp');
//         $uploaded = $disk->put($fileName, $csvData);

//         if ($uploaded) {
//             // Log successful upload
//             Log::info('CSV file successfully uploaded to SFTP', ['fileName' => $fileName]);

//             return response()->json(['message' => 'CSV uploaded to SFTP successfully.']);
//         } else {
//             // Log failure to upload
//             throw new Exception("Failed to upload CSV to SFTP.");
//         }
//     } catch (Exception $e) {
//         // Log detailed error with exception
//         Log::error('Error uploading CSV to SFTP: ' . $e->getMessage(), [
//             'exception' => $e,
//             'fileName' => isset($fileName) ? $fileName : 'N/A',
//             'csvDataLength' => isset($csvData) ? strlen($csvData) : 'N/A',
//             'organisation' => isset($organisation->name) ? $organisation->name : 'N/A',
//         ]);
//         return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
//     }
// }








}

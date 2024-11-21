<?php

namespace App\Http\Controllers\Qms\Ipqa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Qms\Ipqa\QmsIpqaTemps;
use App\Models\Qms\Ipqa\QmsIpqaPsmts;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmGrpcs;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Sys\Usrm\SysUsrmUsers;
use Illuminate\Support\Facades\DB;
use App\Models\Qms\Role\QmsRoleAsgns;
class QmsIpqaPsmtsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('qms.ipqa.psmts.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $organisations = SysOrgaCtrls::get();

        $groups = SysUsrmGrpcs::get();

        return response()->json(['data' => view('qms.ipqa.psmts.create', compact('organisations', 'groups', 'organisation'))->render()]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {        
        // Validate the incoming request
        $validate = Validator::make($request->all(), [
            'date' => 'required',
            'customer' => 'required',
            'model' => 'required',
            'line' => 'required',
            'wo' => 'required',
            'wo_type' => 'required',
            'shift' => 'required',
            'templates' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json(['status'=> 1, 'errors' => $validate->errors()->all()]);
        }

        $checkQuery = QmsRoleAsgns::where('organisation_id', $organisation->id)
        ->where('username', Auth::user()->username)
        ->where('role_id', 1)
        ->count();
        if ($checkQuery == 0){
            return response()->json(['status'=> 1,  'message' => "No permission to add new checksheet"]);
        }

        // Check if the work order already exists with the same shift
        $woValue = strtoupper($request->wo);
        $existingRecord = QmsIpqaPsmts::where('wo', $woValue)
                                    ->where('shift', $request->shift)
                                    ->first();

        if ($existingRecord) {
            return response()->json(['status' => 1, 'message' => "Duplicate Work Order value: " . $woValue]);
        }

        // Generate new filename
        $newFileName = $woValue . "_" . str_replace(' ', '_', $request->line) . '.xlsx';

        // Retrieve the template details from QmsIpqaTemps using the template_id
        $template = QmsIpqaTemps::find($request->templates);
        if (!$template) {
            return response()->json(['status' => 1, 'message' => 'Template not found']);
        }

        // Define the template path based on folder_key and file_name
        $templatePath = storage_path('app/public/excel/ipqa_template/' . $template->folder_key . '/' . $template->file_name);

        // Load the existing template
        try {
            $spreadsheet = IOFactory::load($templatePath);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'message' => 'Error loading template: ' . $e->getMessage()]);
        }

        // Modify the spreadsheet
        $sheet = $spreadsheet->getActiveSheet();
        // $sheet->setCellValue('A2', 'Line: ' . $request->line);
        // $sheet->setCellValue('B2', 'Date: ' . date('d/m/Y'));
        // $sheet->setCellValue('A4', 'Model: ' . $request->model);
        // $sheet->setCellValue('B4', 'Work Order: ' . $woValue);
        // $sheet->setCellValue('E2', Auth::user()->name);  // Assuming authenticated user for name

        // Define the folder path for saving the modified file
        $saveFolderPath = 'public/excel/ipqa/';

        // Use storeAs to save the modified file
        $savePath = $saveFolderPath . $newFileName;

        try {
            // Save the modified spreadsheet to the specified path using storeAs
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            Storage::put($savePath, '');
            $writer->save(storage_path('app/' . $savePath));
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'message' => 'Error saving the modified file: ' . $e->getMessage()]);
        }
        if ($request->wo_type == "C"){
            QmsIpqaPsmts::create([
                'organisation_id' => $organisation->id,
                'created_by' => Auth::user()->name,
                'updated_by' => Auth::user()->name,
                'date' => $request->date,
                'cus_wo' => $woValue,
                'wo' => $request->wo_sel,
                'customer' => $request->customer,
                'line' => $request->line,
                'model' => $request->model,
                'file_name' => $newFileName,
                'step' => 1, // Set step as 1 by default
                'shift' => $request->shift,
                'template_id' => $request->templates,
            ]);
        } else {
            // Create a new entry in the QmsIpqaPsmts model
            QmsIpqaPsmts::create([
                'organisation_id' => $organisation->id,
                'created_by' => Auth::user()->name,
                'updated_by' => Auth::user()->name,
                'date' => $request->date,
                'wo' => $woValue,
                'customer' => $request->customer,
                'line' => $request->line,
                'model' => $request->model,
                'file_name' => $newFileName,
                'step' => 1, // Set step as 1 by default
                'shift' => $request->shift,
                'template_id' => $request->templates,
            ]);            
        }
        
        return response()->json(['status' => 2, 'message' => 'Checksheet Created', 'filePath' => $savePath]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');
    
        // Start building the query
        $query = QmsIpqaPsmts::query()->where('qms_ipqa_psmts.organisation_id', $organisation->id);
    
        // Join with QmsIpqaTemps to get the template_name (or version_name)
        $query->join('qms_ipqa_temps', 'qms_ipqa_psmts.template_id', '=', 'qms_ipqa_temps.id')
              ->select(
                  'qms_ipqa_psmts.*',  // Select all columns from QmsIpqaPsmts
                  'qms_ipqa_temps.version_name as template_name'  // Select template_name (version_name) from QmsIpqaTemps
              );
    
        // Filter by version_name if a name is provided
        if (!empty($name)) {
            $query->where('qms_ipqa_psmts.version_name', 'LIKE', "%{$name}%");
        }
    
        $totalRecords = $query->count();
        $filteredRecords = $query->count();
    
        // Fetch the paginated results
        $mainModule = $query->orderBy('qms_ipqa_psmts.id', 'DESC')
                            ->offset($start)
                            ->limit($length)
                            ->get();
    
        // Map the result to include the fields and the template_name from the joined table
        $mainModule = $mainModule->map(function ($item) {
            return [
                'id' => $item->id,
                'organisation_id' => $item->organisation_id,
                'created_by' => $item->created_by,
                'updated_by' => $item->updated_by,
                'date' => $item->date,
                'wo' => $item->wo,
                'customer' => $item->customer,
                'line' => $item->line,
                'model' => $item->model,
                'cus_wo' => $item->cus_wo,
                'file_name' => $item->file_name,
                'step' => $item->step,
                'shift' => $item->shift,
                'download_date' => $item->download_date,
                'submit_date' => $item->submit_date,
                'verify_card_id' => $item->verify_card_id,
                'verify_date' => $item->verify_date,
                'card_id' => $item->card_id,
                'template_id' => $item->template_id,
                'template_name' => $item->template_name,  // Add the template name from the joined table
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

    public function getWorkOrderDetails(Request $request, SysOrgaCtrls $organisation)
    {
        $wo = $request->wo;

         // Define the query
        $query = 
        "SELECT replace(LEFT(t_pdno,3),'TMS','WDC') as Customer, 
                t_dsca as 'Description', 
                t_cwar as 'Line_No'
        FROM erp.dbo.ttisfc001800 a WITH (NOLOCK)
        LEFT JOIN erp.dbo.ttcibd001800 b WITH (NOLOCK) ON b.t_item = a.t_mitm
        WHERE a.t_pdno = ?";

        // Execute the query using the sqlsrv connection
        $mainModule = DB::connection('sqlsrv')->select($query, [$wo]);

        // Check if any rows are returned
        if (empty($mainModule)) {
            // Return error message if no rows are found
            return response()->json([
                'success' => false,
                'message' => 'Work order invalid, Please make sure this Work Order is in the Infor LN Production Order Module!'
            ]);
        }

        // Initialize the table structure
        $data = '<table class="table table-sm" id="syncTable">
                    <thead>
                    <tr>
                        <th scope="col">Customer</th>
                        <th scope="col">Model</th>
                        <th scope="col">Line</th>
                    </tr>
                    </thead>
                    <tbody>';

        // Loop through the result and build the table rows
        foreach ($mainModule as $item) {
            $data .= '<tr>
                        <td>' . $item->Customer . '</td>
                        <td>' . $item->Description . '</td>
                        <td><input class="form-control form-control-sm" value="' . $item->Line_No . '" name="line"></td>
                    </tr>';
        }

        // Close the table body
        $data .= '</tbody></table>';

        // Return the data in JSON format
        return response()->json([
            'success' => true,
            'data' => $data
        ]);

    }
    public function getCustomerWorkOrderDetails(Request $request)
    {
        // Retrieve the cus_wo parameter from the request
        $cusWo = strtoupper($request->input('cus_wo'));

        // Initialize the base HTML for the dropdown
        $data = '<label class="col-sm-3 col-form-label">TMSMT Work Order</label>
                <div class="col-sm-9">
                    <select class="form-select" onChange="woSelect(this)" name="wo_sel">
                        <option value="1">Select TMSMT Work Order</option>';

        // Initialize status
        $status = 2;

        // Define the SQL query with parameter binding
        $query = "
            SELECT a.t_pdno AS workorder, b.t_dsca AS Description
            FROM erp.dbo.ttisfc001800 a WITH (NOLOCK)
            LEFT JOIN erp.dbo.ttcibd001800 b WITH (NOLOCK) ON b.t_item = a.t_mitm
            LEFT JOIN erp.dbo.ttttxt010800 c WITH (NOLOCK) ON a.t_txta = c.t_ctxt
            WHERE LTRIM(RTRIM(CAST (c.t_text AS CHAR(12)))) = ?
        ";

        // Execute the query using Laravel's DB facade (SQL Server connection)
        $results = DB::connection('sqlsrv')->select($query, [$cusWo]);

        // Check if the query result contains more than 2 rows
        if (count($results) > 2) {
            foreach ($results as $row) {
                // Check for 'TOP' or 'BOT' in the description, skip if present
                if (!str_contains($row->Description, 'TOP') && !str_contains($row->Description, 'BOT')) {
                    $data .= '<option value="' . $row->workorder . '">' . $row->workorder . ' (' . $row->Description . ')</option>';
                }
            }
        } elseif (count($results) > 0) {
            // If rows are returned but not more than 2, loop through them
            foreach ($results as $row) {
                $data .= '<option value="' . $row->workorder . '">' . $row->workorder . ' (' . $row->Description . ')</option>';
            }
        } else {
            // Handle the case when no results are found
            $data = '<label class="col-sm-12 col-form-label text-center text-danger">Work order invalid, Please make sure this Work Order is in Infor LN Production Order Module!</label>';
            $status = 1;
        }

        // Close the select tag
        $data .= '</select></div>';

        // Return JSON response with the HTML data and status
        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }

    public function getWOWithCustomerWODetails(Request $request)
    {
        $data = '<table class="table table-sm" id="syncTable">
        <thead>
          <tr>
            <th scope="col">Customer</th>
            <th scope="col">Model</th>
            <th scope="col">Line</th>
          </tr>
        </thead>
        <tbody>';
    
        // Initialize status
        $status = 2;
    
        // Define the SQL query with parameter binding
        $query = "
            SELECT REPLACE(LEFT(t_pdno, 3), 'TMS', 'WDC') AS Customer, 
                   b.t_dsca AS Description, 
                   a.t_cwar AS 'Line No'
            FROM erp.dbo.ttisfc001800 a WITH (NOLOCK)
            LEFT JOIN erp.dbo.ttcibd001800 b WITH (NOLOCK) ON b.t_item = a.t_mitm
            WHERE a.t_pdno = ?
        ";
    
        // Execute the query using Laravel's DB facade (SQL Server connection)
        $results = DB::connection('sqlsrv')->select($query, [strtoupper($request->input('wo'))]);
    
        // Check if any rows are returned
        if (count($results) > 0) {
            // Loop through the results and construct the table rows
            foreach ($results as $row) {
                $data .= '<tr>
                            <td>' . $row->Customer . '</td>
                            <td>' . $row->Description . '</td>
                            <td>
                              <select class="form-select" name="line">
                                <option value="Box Build 12">Box Build 12</option>
                                <option value="Box Build 13">Box Build 13</option>
                                <option value="Box Build 1 Fortune">Box Build 1 Fortune</option>
                              </select>
                            </td>
                          </tr>';
            }
        } else {
            // If no results are found, display a message
            $data = '<div class="mb-3 row">
                       <label class="col-sm-12 col-form-label text-center text-danger">
                         Work order invalid, Please make sure this Work Order is in Infor LN Production Order Module!
                       </label>
                     </div>';
            $status = 1;
        }
    
        // Close the table
        $data .= '</tbody></table>';
    
        // Return JSON response with the data and status
        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }

    public function getTemplate(Request $request, SysOrgaCtrls $organisation)
    {
        // Get the category from the request
        $category = $request->input('category');

        // Query the QmsIpqaTemps model based on the selected category and organization
        $query = QmsIpqaTemps::query()
            ->where('categories', $category)
            ->where('organisation_id', $organisation->id)
            ->orderBy('id', 'DESC');

        // Execute the query and get the results
        $mainModule = $query->get();

        // Check if any templates were found
        if ($mainModule->isEmpty()) {
            // Return a message if no templates are found
            return response()->json([
                'success' => false,
                'message' => "Please add a template for $category"
            ]);
        }

        // Map the results to the desired format
        $mainModule = $mainModule->map(function ($item) {
            return [
                'id' => $item->id,
                'version_name' => $item->version_name,
                'file_name' => $item->file_name,
            ];
        });

        // Return the response as JSON
        return response()->json([
            'success' => true,
            'data' => $mainModule
        ]);
    }

    public function stepOne(SysOrgaCtrls $organisation, Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'id' => 'required',
        ]);

        // Retrieve the record based on id (esc_id) and step (should be 1)
        $record = QmsIpqaPsmts::where('id', $request->id)
                    ->first();

        // If no record found, return error
        if (!$record) {
            return response()->json([
                'message' => 'Error checking for duplicates: Record not found or step is not 1',
                'type' => 1
            ]);
        }
        $checkQuery = QmsRoleAsgns::where('organisation_id', $organisation->id)
        ->where('username', Auth::user()->username)
        ->where('role_id', 2)
        ->count();

        if ($checkQuery == 0){
            return response()->json(['status'=> 1,  'message' => "No permission to verify checksheet"]);
        }

        // File path in storage (assuming the files are stored in 'storage/app/public/excel/ipqa/')
        $filePath = storage_path('app/public/excel/ipqa/' . $record->file_name);
        
        // Check if the file exists
        if (!file_exists($filePath)) {
            return response()->json([
                'data' => 'File not found on the server.',
                'type' => 1
            ]);
        }

        try {
            // Load the spreadsheet
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Populate cells from the record
            $sheet->setCellValue('A2', 'Line: ' . $record->line);
            $sheet->setCellValue('B2', 'Date: ' . date('d/m/Y'));
            $sheet->setCellValue('A4', 'Model: ' . $record->model);
            $sheet->setCellValue('B4', 'Work Order: ' . $record->wo);
            $sheet->setCellValue('E2', Auth::user()->name);  // Assuming authenticated user for name

            // Update the step to 2 and set the download date
            $record->update([
                'step' => 2,
                'download_date' => now()  // Set the current timestamp
            ]);

            // Output the spreadsheet directly as a downloadable response
            return response()->streamDownload(function() use ($spreadsheet) {
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            }, $record->file_name, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $record->file_name . '"',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'data' => 'Error processing the file: ' . $e->getMessage(),
                'type' => 1
            ]);
        }
    }

    public function getStep2Modal(SysOrgaCtrls $organisation, Request $request)
    {
        // Retrieve the record with the specific ID
        $existingRecord = QmsIpqaPsmts::where('id', $request->id)
            ->select('file_name')  // Only select the file_name column
            ->first();

        // Check if the record exists
        if (!$existingRecord) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        // Return the view with the required data
        return response()->json([
            'data' => view('qms.ipqa.psmts.getStep2Modal', [
                'organisation' => $organisation,
                'file_name' => $existingRecord->file_name,  // Pass the file_name to the view
                'id' => $request->id,
            ])->render()
        ]);
    }

    public function uploadStep2(SysOrgaCtrls $organisation, Request $request)
    {
        // Validate incoming request
        $validate = Validator::make($request->all(), [
            'ipqa_file' => 'required|file|mimes:xlsx|max:2048',
            'id' => 'required|integer',  // Ensure id is provided and valid
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        // Retrieve the record to update the step and replace the file
        $record = QmsIpqaPsmts::where('id', $request->id)->first();

        if (!$record) {
            return response()->json(['status' => 1, 'message' => 'Record not found']);
        }

        try {
            // Handle file upload and check if the uploaded file name matches the one in the database
            if ($request->hasFile('ipqa_file')) {
                $file = $request->file('ipqa_file');
                $checkQuery = QmsRoleAsgns::where('organisation_id', $organisation->id)
                ->where('username', Auth::user()->username)
                ->where('role_id', 2)
                ->count();
        
                if ($checkQuery == 0){
                    return response()->json(['status'=> 1,  'message' => "No permission to upload checksheet"]);
                }
                $uploadedFilename = $file->getClientOriginalName();  // Get the uploaded file's name

                // Compare the uploaded file's name with the database file_name
                if ($uploadedFilename !== $record->file_name) {
                    return response()->json(['status' => 1, 'message' => 'Uploaded file name does not match the existing file name.']);
                }

                // If the file names match, proceed to store the file and update the record
                $file->storeAs('public/excel/ipqa/', $uploadedFilename);  // Replace the existing file with the same name

                // Update the step and submit date
                $record->update([
                    'card_id' =>  Auth::user()->username,
                    'step' => 3,  // Update the step to 3
                    'submit_date' => now(),  // Set the current timestamp
                ]);

                return response()->json([
                    'status' => 2,
                    'message' => 'Checksheet uploaded and updated successfully',
                ]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function stepThree(SysOrgaCtrls $organisation, Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'id' => 'required|integer',
        ]);

        // Retrieve the record based on id and ensure the step is 3
        $record = QmsIpqaPsmts::where('id', $request->id)
                    ->first();

        $checkQuery = QmsRoleAsgns::where('organisation_id', $organisation->id)
        ->where('username', Auth::user()->username)
        ->whereIn('role_id', [3, 4])
        ->count();

        if ($checkQuery == 0){
            return response()->json(['status'=> 1,  'message' => "No permission to upload checksheet"]);
        }

        // If no record found, return error
        if (!$record) {
            return response()->json([
                'data' => 'Error: Record not found or step is not 3',
                'type' => 1
            ]);
        }

        // File path for the existing Excel file
        $filePath = storage_path('app/public/excel/ipqa/' . $record->file_name);

        // Check if the file exists
        if (!file_exists($filePath)) {
            return response()->json([
                'data' => 'Error: File not found on the server.',
                'type' => 1
            ]);
        }

    
        // Try to update the database record (set step and download date)
        try {
            $record->update([
                'step' => 4,  // Update step to 4
                'download_date' => now()  // Set the current timestamp
            ]);

            // Return the file for download after updating
            return response()->download($filePath);

        } catch (Exception $e) {
            return response()->json([
                'data' => 'Error executing update query: ' . $e->getMessage(),
                'type' => 1
            ]);
        }
    }


    public function stepFour(SysOrgaCtrls $organisation, Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'id' => 'required',
        ]);

        // Retrieve the record based on id (esc_id) and step (should be 1)
        $record = QmsIpqaPsmts::where('id', $request->id)
                    ->where('step', 4)
                    ->first();

        // If no record found, return error
        if (!$record) {
            return response()->json([
                'data' => 'Error checking for duplicates: Record not found or step is not 4',
                'type' => 1
            ]);
        }
        $checkQuery = QmsRoleAsgns::where('organisation_id', $organisation->id)
        ->where('username', Auth::user()->username)
        ->where('role_id', 3)
        ->count();

        if ($checkQuery == 0){
            return response()->json(['status'=> 1,  'message' => "No permission to complete checksheet"]);
        }
        $filePath = storage_path('app/public/excel/ipqa/' . $record->file_name);

        try {
            // Load the Excel template
            $spreadsheet = IOFactory::load($filePath);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'message' => 'Error loading template: ' . $e->getMessage()]);
        }

        // Modify the spreadsheet (e.g., updating user info in cell J2)
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('J2', Auth::user()->name);  // Modify a specific cell

        // Define the save path for the modified file (overwriting the existing file)
        $saveFolderPath = 'public/excel/ipqa/';
        $savePath = $saveFolderPath . $record->file_name;  // Keep the same file name

        try {
            // Create the writer for saving the Excel file
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

            // Save the modified file back to storage (overwriting)
            $writer->save(storage_path('app/' . $savePath));
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'message' => 'Error saving the modified file: ' . $e->getMessage()]);
        }
        try {
            // Update the step to 2 and set the download date
            $record->update([
                'verify_card_id' => Auth::user()->username,
                'step' => 5,
                'verify_date' => now(),  // Set the current timestamp
            ]);

            // Return the file for download
            return response()->json([
                'data' => 'Checklist Complete Verified',
                'type' => 2
            ]);

        } catch (Exception $e) {
            return response()->json([
                'data' => 'Error executing update query: ' . $e->getMessage(),
                'type' => 1
            ]);
        }
    }

    public function stepFive(SysOrgaCtrls $organisation, Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'id' => 'required',
        ]);

        // Retrieve the record based on id (esc_id) and step (should be 1)
        $record = QmsIpqaPsmts::where('id', $request->id)
                    ->where('step', 5)
                    ->first();

        // If no record found, return error
        if (!$record) {
            return response()->json([
                'data' => 'Error checking for duplicates: Record not found or step is not 2',
                'type' => 1
            ]);
        }
        
        $checkQuery = QmsRoleAsgns::where('organisation_id', $organisation->id)
        ->where('username', Auth::user()->username)
        ->where('role_id', 4)
        ->count();

        if ($checkQuery == 0){
            return response()->json(['status'=> 1,  'message' => "No permission to download checksheet"]);
        }

        // File path in storage (assuming the files are stored in 'storage/app/public/excel/ipqa/')
        $filePath = storage_path('app/public/excel/ipqa/' . $record->file_name);

        // Check if the file exists
        if (!file_exists($filePath)) {
            return response()->json([
                'data' => 'File not found on the server.',
                'type' => 1
            ]);
        }

        try {
            // Return the file for download
            return response()->download($filePath);
        } catch (Exception $e) {
            return response()->json([
                'data' => 'Error executing update query: ' . $e->getMessage(),
                'type' => 1
            ]);
        }
    }

    public function downloadChecklist(SysOrgaCtrls $organisation, Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'id' => 'required',
        ]);

        // Retrieve the record based on id (esc_id) and step (should be 1)
        $record = QmsIpqaPsmts::where('id', $request->id)
                    ->where('step', 4)
                    ->first();

        // If no record found, return error
        if (!$record) {
            return response()->json([
                'data' => 'Error checking for duplicates: Record not found or step is not 4',
                'type' => 1
            ]);
        }

        try {
            // Update the step to 2 and set the download date
            $record->update([
                'step' => 5,
                'verify_date' => now(),  // Set the current timestamp
            ]);

            // Return the file for download
            return response()->json([
                'data' => 'Checklist Complete Verified',
                'type' => 2
            ]);

        } catch (Exception $e) {
            return response()->json([
                'data' => 'Error executing update query: ' . $e->getMessage(),
                'type' => 1
            ]);
        }
    }
}

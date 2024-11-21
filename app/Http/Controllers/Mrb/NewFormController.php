<?php

namespace App\Http\Controllers\Mrb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmUsers;
use App\Models\Qms\Role\QmsRoleAsgns;
use App\Models\Qms\Role\QmsRoleRolls;
use App\Models\Mrb\Emrb\MrbEmrbHeaders;
use App\Models\Mrb\Emrb\MrbEmrbStatus;
use App\Models\Mrb\Emrb\MrbEmrbContents;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Mrb\Lvlc\MrbLvlcRolls;
use App\Models\Mrb\Lvlc\MrbLvlcGalls;
use App\Models\Mrb\Dide\MrbDideDisps;
use App\Models\Mrb\Dide\MrbDideDefcs;
use App\Models\Sys\Usrm\SysUsrmGrpcs;
use Illuminate\Support\Facades\File;

class NewFormController extends Controller
{
    public function rejectMrb(SysOrgaCtrls $organisation, Request $request)
    {
        // Validate the incoming request
        $validate = Validator::make($request->all(), [
            'mrb_number' => 'required|string',
            'remark' => 'nullable|string',
        ]);
        if ($validate->fails()) {
            return response()->json(['status'=> 1, 'errors' => $validate->errors()->all()]);
        }
        $current_level = MrbEmrbStatus::where('form_id', $request->mrb_number)->max('level');
        $new_level = $current_level + 1;
        $mrbLevel = MrbEmrbStatus::create([
            'form_id'=> $request->mrb_number,
            'organisation_id' => $organisation->id, // Replace if needed
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
            'remark' => $request->remark,
            'level' => $new_level,    
            'status' => 'void',      
        ]);
        return response()->json(['status' => 2, 'message' => "This MRB have been voided"]);
    }
    public function approveMrb(SysOrgaCtrls $organisation, Request $request)
    {
        // Validate the incoming request
        $validate = Validator::make($request->all(), [
            'mrb_number' => 'required|string',
            'remark' => 'nullable|string',
        ]);
        if ($validate->fails()) {
            return response()->json(['status'=> 1, 'errors' => $validate->errors()->all()]);
        }
        $current_level = MrbEmrbStatus::where('form_id', $request->mrb_number)->max('level');
        $new_level = $current_level + 1;
        $mrbLevel = MrbEmrbStatus::create([
            'form_id'=> $request->mrb_number,
            'organisation_id' => $organisation->id, // Replace if needed
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
            'remark' => $request->remark,
            'level' => $new_level,          
        ]);
        return response()->json(['status' => 2, 'message' => "Approved Succesfully"]);
    }
    public function index(SysOrgaCtrls $organisation, $newERMBId)
    {
        $mrbheader = [];
        $mrbLevel = [];
        $approver_list = [];
        $currentStep = -1;
        $is_user = false;
        if($newERMBId != 0){
            
            //Try to search
            $mrbheader = MrbEmrbHeaders::where('mbr_number', $newERMBId)->first();
            $mrbLevel = MrbEmrbStatus::where('form_id', $newERMBId)->get();
            $department = SysUsrmGrpcs::where('name',$mrbheader->department)->first();
            $currentStep = $mrbLevel->max('level');
            
            $approver_list = MrbLvlcGalls::where('group_id', $department->id)
            ->join('sys_usrm_users', 'mrb_lvlc_galls.username', '=', 'sys_usrm_users.username')
            ->select('mrb_lvlc_galls.*', 'sys_usrm_users.name as user_name')
            ->where('level_id', $currentStep + 1)
            ->get();

            $is_user = $approver_list->contains('username', Auth::user()->username);
        }
        $currentStep = $currentStep ?? -1;
        $tabs = MrbLvlcRolls::query()->where("organisation_id", $organisation->id)->get();
        // $newERMBID = Model::find 

        return view('mrb.emrb.index', compact('organisation', 'tabs','currentStep','mrbheader','mrbLevel','approver_list', 'is_user'));
    }
    public function submitMrbForm(SysOrgaCtrls $organisation, Request $request)
    {        
        // Validate the incoming request
        $validate = Validator::make($request->all(), [
            'mrb_number' => 'required|string',
        ]);
        if ($validate->fails()) {
            return response()->json(['status'=> 1, 'errors' => $validate->errors()->all()]);
        }
        

        $mrbLevel = MrbEmrbStatus::create([
            'form_id'=> $request->mrb_number,
            'organisation_id' => $request->organisation_id, // Replace if needed
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
            'remark' => '',
            'level' => 0,          
        ]);
        $mrbHeader = MrbEmrbHeaders::where('mbr_number',$request->mrb_number)->first();
        if ($mrbHeader) {
            // Update the date_submit field to the current timestamp
            $mrbHeader->date_submit = now();
            $mrbHeader->save();
        }
        return response()->json(['status' => 2, 'message' => "Submit Succesfully"]);
    }
    public function store(SysOrgaCtrls $organisation, Request $request)
    {        
        // Validate the incoming request
        $validate = Validator::make($request->all(), [
            'cost_born_by' => 'required|string',
            'mes_wo' => 'required|string',
            'plant' => 'required|string',
            'line' => 'required|string',
            'model' => 'required|string',
            'type' => 'required|string',
            'customer' => 'required|string',
            'initiator' => 'required|string',
            'department' => 'required|string',
            'transfer_no' => 'nullable|string',
            'mrb_number' => 'nullable|string',
        ]);
        if ($validate->fails()) {
            return response()->json(['status'=> 1, 'errors' => $validate->errors()->all()]);
        }
        $newMrbNumber = MrbEmrbHeaders::generateMrbNumber();

        $mrbRecord = MrbEmrbHeaders::create([
            'mbr_number' => $newMrbNumber,
            'organisation_id' => $request->organisation_id, // Replace if needed
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
            'cost_born_by' => $request->cost_born_by,
            'mes_kitlist' => $request->mes_wo,
            'plant' => $request->plant,
            'line' => $request->line,
            'model' => $request->model,
            'type' => $request->type,
            'initiator' => $request->initiator,
            'department' => $request->department,
            'organisation_name' => $request->organisation_name,
        ]);


        
        // $mrbLevel = MrbEmrbStatus::create([
        //     'form_id'=> $newMrbNumber,
        //     'organisation_id' => $request->organisation_id, // Replace if needed
        //     'created_by' => Auth::user()->name,
        //     'updated_by' => Auth::user()->name,
        //     'remark' => '',
        //     'level' => 0,          
        // ]);
        return response()->json(['status' => 2, 'message' => $newMrbNumber]);
    }

    public function create(SysOrgaCtrls $organisation, $customer, $mrbFormId)
    {

        $part1 = "http://192.168.1.97:9999";
        $part2 = "/api/dataportal/invoke";
    
        // Combine variables into one URL
        $apiUrl = $part1 . $part2;

        // Attempt to get the ticket
        $ticket = $this->getTicket($apiUrl);

        if ($ticket === false) {
            return response()->json(['error' => 'Unable to retrieve ticket.'], 500);
        }

        $response  = $this->getPartNumber($apiUrl, $customer, $ticket);
        if (isset($response['Result']['WorkOrderBomDatas'])) {
            $wo = $response['Result']['WorkOrderBomDatas']; // Extract WorkOrderBomDatas
        }
        $mes_wo = $customer;
        $defects = MrbDideDefcs::query()->get();
        $dispositions = MrbDideDisps::query()->get();
        return response()->json(['data' => view('mrb.emrb.create', compact('organisation', 'wo','defects','dispositions','mrbFormId','mes_wo'))->render()]);
    }

    public function edit(SysOrgaCtrls $organisation, $customer, $mrbFormId)
    {
            
        $wo = [];
        $content = MrbEmrbContents::where('id', $mrbFormId)->first();
        $mes_wo = $customer;
        $defects = MrbDideDefcs::query()->get();
        $dispositions = MrbDideDisps::query()->get();
        $quantity = $this->retrievePartQuantity($content->part_number);
        return response()->json(['data' => view('mrb.emrb.create', compact('organisation', 'wo','defects','dispositions','content','quantity','mes_wo'))->render()]);
    }
    
    public function showEmrbContent(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'mrbNumber' => 'required|string'
        ]);

        // Check if validation fails
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'errors' => $validate->errors()->all()]);
        }

        // Query to join MrbDideDefcs and MrbDideDisps directly and select needed fields
        $mrbContent = MrbEmrbContents::select(
                'mrb_emrb_contents.*',
                'mrb_dide_defcs.name as defect_name',
                'mrb_dide_disps.name as disposition_name'
            )
            ->leftJoin('mrb_dide_defcs', 'mrb_emrb_contents.defect', '=', 'mrb_dide_defcs.id')
            ->leftJoin('mrb_dide_disps', 'mrb_emrb_contents.disposition', '=', 'mrb_dide_disps.id')
            ->where('mrb_emrb_contents.form_id', $request->mrbNumber)
            ->get();

        // Map the results for additional formatting if needed
        $mrbContent = $mrbContent->map(function ($item) {
            return [
                'id' => $item->id,
                'form_id' => $item->form_id,
                'part_number' => $item->part_number,
                'description' => $item->description,
                'location' => $item->location,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'amount' => $item->amount,
                'currency' => $item->currency,
                'defect' => $item->defect,
                'defect_name' => $item->defect_name, // Joined field
                'disposition' => $item->disposition,
                'disposition_name' => $item->disposition_name, // Joined field
                'root_cause' => $item->root_cause,
                'correction' => $item->correction,
                'remark' => $item->remark,
                'file_path' => $item->file_path,
                'organisation_id' => $item->organisation_id,
                'created_by' => $item->created_by,
                'updated_by' => $item->updated_by,
            ];
        });

        $json_data = [
            "data" => $mrbContent
        ];

        return response()->json($json_data);
    }

    public function updateEmrbContent(SysOrgaCtrls $organisation, Request $request)
    {
        $part1 = "http://192.168.1.97:9999";
        $part2 = "/api/dataportal/invoke";
    
        // Combine variables into one URL
        $apiUrl = $part1 . $part2;

        $ticket = $this->getTicket($apiUrl);
        //Need Pass In Max Part Number Value as well
        $validate = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'amount' => 'required|numeric', 
            'defect' => 'required|array',
            'defect.*' => 'string',
            'disposition' => 'required|array',
            'disposition.*' => 'string',
            'root_cause' => 'required|string',
            'correction' => 'required|string',
            'remark' => 'nullable|string',
            'mes_wo' => 'required|string',
            'id' => 'required|string'
        ]);
 
        // Check if validation fails
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'errors' => $validate->errors()->all()]);
        }


        $mrbEmrbContent = MrbEmrbContents::find($request->id);

        if (!$mrbEmrbContent) {
            return response()->json(['status' => 1, 'message' => 'Content not found']);
        }

        $max_part_number = $this->getMaxQuantity($apiUrl, $request->mes_wo, $mrbEmrbContent->part_number, $ticket);
        $max_part_number = intval($max_part_number["Result"]);
        $part_num_quantity = MrbEmrbContents::where('form_id', $mrbEmrbContent->form_id)
        ->where('part_number', $mrbEmrbContent->part_number)
        ->where('id', '!=', $request->id) // Add this condition
        ->sum('quantity');
        $currentTotal = intval($part_num_quantity) + intval($request->quantity);
        if($currentTotal > $max_part_number){
            return response()->json(['status' => 1, 'errors' => ["Part Number Exceed Quantity"]]);
        }
        $file = $request->file('fileToUpload');
        $filename = '';
        if ($file != null) {
            // Retrieve the file path and form_id from the record
            $filePath = $mrbEmrbContent->file_path;
            $formId = $mrbEmrbContent->form_id;
        
            // Construct the full path to the file in the public directory
            $fullFilePath = public_path("images/eMrbContent/" . $formId . "/" . $filePath);
        
            // Check if the file exists and delete it
            if (File::exists($fullFilePath)) {
                File::delete($fullFilePath);
            }
        
            // Define the folder path using the folder key in the public directory
            $folderPath = 'images/eMrbContent/' . $formId . '/';
            
            // Ensure the directory exists (optional; `move()` will create it if it doesn't)
            if (!file_exists(public_path($folderPath))) {
                mkdir(public_path($folderPath), 0777, true);
            }
        
            // Use the original file name instead of defining a custom name
            $filename = $file->getClientOriginalName();
            
            // Move the file to the public directory with the original filename
            $file->move(public_path($folderPath), $filename);
            
            // Store the file path if needed
            $filePath = $folderPath . $filename;
        }
        
        $updateData = [
            'quantity' => $request->quantity,
            'amount' => $request->amount,
            'defect' => is_array($request->defect) ? $request->defect[0] : $request->defect,
            'disposition' => is_array($request->disposition) ? $request->disposition[0] : $request->disposition,
            'root_cause' => $request->root_cause,
            'correction' => $request->correction,
            'remark' => $request->remark,
            'organisation_id' => $organisation->id, // Replace if needed
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ];
        
        // Conditionally add the file path if it's not empty
        if (!empty($filename)) {
            $updateData['file_path'] = $filename;
        }
        $mrbEmrbContent->update($updateData);
        return response()->json(['status' => 2, 'message' => "Update Successfully"]);
    }
    public function storeEmrbContent(SysOrgaCtrls $organisation, Request $request)
    {
        $part1 = "http://192.168.1.97:9999";
        $part2 = "/api/dataportal/invoke";
    
        // Combine variables into one URL
        $apiUrl = $part1 . $part2;

        $ticket = $this->getTicket($apiUrl);
        //Need Pass In Max Part Number Value as well
        $validate = Validator::make($request->all(), [
            // Add validation for additional form data
            'part_number' => 'required|array', // Ensure it's an array
            'part_number.*' => 'string', // Ensure each part number is a string
            'description' => 'required|string',
            'location' => 'nullable|string',
            'quantity' => 'required|numeric|min:1', // Ensure quantity is a number
            'unit_price' => 'required|numeric', // Ensure unit price is a number
            'amount' => 'required|numeric', // Ensure amount is a number
            'currency' => 'required|string',
            'defect' => 'required|array', // Ensure defect is an array
            'defect.*' => 'string', // Ensure each defect value is a string
            'disposition' => 'required|array', // Ensure disposition is an array
            'disposition.*' => 'string', // Ensure each disposition value is a string
            'root_cause' => 'required|string',
            'correction' => 'required|string',
            'remark' => 'nullable|string',
            'fileToUpload' => 'file|max:2048',
            'mrb_form_id' => 'required|string'
        ]);
        //Validation (Make Sure Quantity Total Does Not Exceed Max)


        // Check if validation fails
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'errors' => $validate->errors()->all()]);
        }
        $max_part_number = $this->getMaxQuantity($apiUrl, $request->mes_wo, $request->part_number[0], $ticket);
        $max_part_number = intval($max_part_number["Result"]);
        $part_num_quantity = MrbEmrbContents::where('form_id', $request->mrb_form_id)
        ->where('part_number', $request->part_number[0])
        ->sum('quantity');
        $currentTotal = intval($part_num_quantity) + intval($request->quantity);
        if($currentTotal > $max_part_number){
            return response()->json(['status' => 1, 'errors' => ["Part Number Exceed Quantity"]]);
        }
        $file = $request->file('fileToUpload');

        // Initialize the file path to null if no file is uploaded
        $filename = null;

        if ($file) {
            // Define the folder path within the 'storage/app/public' directory for eMrb files
            $folderPath = 'images/eMrbContent/' . $request->mrb_form_id;
        
            // Create the full directory path under 'public/storage'
            $directoryPath = public_path('storage/' . $folderPath);
        
            // Ensure the directory exists, create it if it doesn't
            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }
        
            // Use the original file name
            $filename = $file->getClientOriginalName();
        
            // Move the file to the desired location within the public directory
            $file->move($directoryPath, $filename);
        
            // Create the relative file path for storage in the database or later use
            $filePath = $folderPath . '/' . $filename;
        
            // Generate a URL to access the stored file
            $fileUrl = asset('storage/' . $folderPath . '/' . $filename);
        
        }

        // Create the record in the database with or without the file name
        $mrbContent = MrbEmrbContents::create([
            'form_id' => $request->mrb_form_id,
            'part_number' => is_array($request->part_number) ? $request->part_number[0] : $request->part_number,
            'description' => $request->description,
            'location' => $request->location,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'defect' => is_array($request->defect) ? $request->defect[0] : $request->defect,
            'disposition' => is_array($request->disposition) ? $request->disposition[0] : $request->disposition,
            'root_cause' => $request->root_cause,
            'correction' => $request->correction,
            'remark' => $request->remark,
            'file_path' => $filename, // This will be null if no file is uploaded
            'organisation_id' => $organisation->id, // Replace if needed
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        // $mrbLevel = MrbEmrbStatus::create([
        //     'form_id'=> $newMrbNumber,
        //     'organisation_id' => $request->organisation_id, // Replace if needed
        //     'created_by' => Auth::user()->name,
        //     'updated_by' => Auth::user()->name,
        //     'remark' => '',
        //     'level' => 0,          
        // ]);
        return response()->json(['status' => 2, 'message' => "Create Successfully"]);
    }

    public function qtyCur(SysOrgaCtrls $organisation, Request $request)
    {
        // Get the part number from the request
        $partNo = $request->part_no;
    
        // The external API URL
        $apiUrl = "http://168.168.1.23/inforLN/api/E-MR/Material_Requistion.php?kitlist=&partno=" . urlencode($partNo);
    
        // Initialize cURL
        $ch = curl_init($apiUrl);
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=UTF-8',
        ]);
    
        // Execute the cURL request
        $response = curl_exec($ch);
    
        // Check for cURL errors
        if (curl_errno($ch)) {
            // Log the error
            \Log::error('Curl error: ' . curl_error($ch));
            curl_close($ch);
    
            // Return a JSON response with an error status
            return response()->json([
                'status' => 1,
                'errors' => ['Failed to retrieve data from the API.']
            ]);
        }
    
        // Close the cURL session
        curl_close($ch);
    
        // Decode the JSON response
        $responseData = json_decode($response, true);
    
        // Check if the response is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Log the error
            \Log::error('JSON decode error: ' . json_last_error_msg());
    
            // Return a JSON response with an error status
            return response()->json([
                'status' => 1,
                'errors' => ['Invalid JSON response from the API.']
            ]);
        }
    
        // Return a JSON response with the API data if successful
        return response()->json([
            'status' => 2,
            'data' => $responseData['data'] ?? []
        ]);
    }

    public function loadWODetails(SysOrgaCtrls $organisation, Request $request)
    {
        $part1 = "http://192.168.1.97:9999";
        $part2 = "/api/dataportal/invoke";
    
        // Combine variables into one URL
        $apiUrl = $part1 . $part2;

        // Attempt to get the ticket
        $ticket = $this->getTicket($apiUrl);

        if ($ticket === false) {
            return response()->json(['error' => 'Unable to retrieve ticket.'], 500);
        }

        $wo = $this->getWOFromMES($apiUrl, $request->wo, $ticket);
        // Return the ticket or perform further actions as needed
        return response()->json([$wo]);
    }

    private function getTicket($apiUrl)
    {
        $userName = "ERP01";
        $password = "Infor2024";
        // $userName = "LLK";
        // $password = "666666";
        $responseData = $this->performLogin($apiUrl, $userName, $password);

        // Check if the response is a boolean (indicating a cURL error)
        if (is_bool($responseData)) {
            return false;
        }

        // Check if the Ticket key exists in the response
        if (isset($responseData['Context']['Ticket'])) {
            return $responseData['Context']['Ticket'];
        } else {
            \Log::error('Error: Ticket not found in API response.');
            return false;
        }
    }
    public function destroyEmrbContent(SysOrgaCtrls $organisation, Request $request){
            // Find the MrbEmrbContent record by the passed ID
            $mrbEmrbContent = MrbEmrbContents::find($request->id);

            if (!$mrbEmrbContent) {
                return response()->json(['status' => 1, 'message' => 'Content not found']);
            }

            // Retrieve the file path and form_id from the record
            $filePath = $mrbEmrbContent->file_path;
            $formId = $mrbEmrbContent->form_id;

            // Construct the full path to the file
            $fullFilePath = storage_path("app/public/images/eMrbContent/" . $formId . "/" . $filePath);

            // Check if the file exists and delete it
            if (File::exists($fullFilePath)) {
                File::delete($fullFilePath);
                $mrbEmrbContent->delete(); // Delete the record only if the file was deleted successfully
                return response()->json(['status' => 2, 'message' => 'File and record deleted successfully']);
            } else {
                return response()->json(['status' => 1, 'message' => 'File not found']);
            }
    }
    private function performLogin($apiUrl, $userName, $password)
    {
        $requestData = [
            'ApiType' => 'AuthenticationController',
            'Method' => 'Login',
            'Parameters' => [
                ['Value' => $userName],
                ['Value' => $password],
            ],
            'Context' => [
                'Ticket' => '内容', // Replace with the actual content for the Ticket
            ],
        ];

        $jsonData = json_encode($requestData);

        // Initialize cURL
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=UTF-8',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            \Log::error('Curl error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        // Close cURL session
        curl_close($ch);

        // Decode the JSON response
        $responseData = json_decode($response, true);
        return $responseData;
    }
    //
    private function getWOFromMES($apiUrl, $wo, $ticket)
    {
        $requestData = [
            'ApiType' => 'ElecWorkOrderApiController',
            'Method' => 'GetWorkOrderInfo',
            'Parameters' => [
                ['Value' => $wo],
            ],
            'Context' => [
                'Ticket' => $ticket, // Replace with the actual content for the Ticket
                'InvOrgId' => 1,
                'Language' => "en"
            ],
        ];

        $jsonData = json_encode($requestData);

        // Initialize cURL
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=UTF-8',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            \Log::error('Curl error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        // Close cURL session
        curl_close($ch);

        // Decode the JSON response
        $responseData = json_decode($response, true);
        return $responseData;
    }

    private function getMaxQuantity($apiUrl, $wo, $part_num, $ticket)
    {
        $requestData = [
            'ApiType' => 'ElecWorkOrderApiController',
            'Method' => 'GetWorkOrderItemQty',
            'Parameters' => [
                ['Value' => $wo],
                ['Value' => $part_num],
            ],
            'Context' => [
                'Ticket' => $ticket, // Replace with the actual content for the Ticket
                'InvOrgId' => 1,
                'Language' => "en"
            ],
        ];

        $jsonData = json_encode($requestData);

        // Initialize cURL
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=UTF-8',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            \Log::error('Curl error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        // Close cURL session
        curl_close($ch);

        // Decode the JSON response
        $responseData = json_decode($response, true);
        return $responseData;
    }
    private function getPartNumber($apiUrl, $mes_wo, $ticket)
    {
        $requestData = [
            'ApiType' => 'ElecWorkOrderApiController',
            'Method' => 'GetWorkOrderBom',
            'Parameters' => [
                ['Value' => $mes_wo],
            ],
            'Context' => [
                'Ticket' => $ticket, // Replace with the actual content for the Ticket
                'InvOrgId' => 1,
                'Language' => "en"
            ],
        ];

        $jsonData = json_encode($requestData);

        // Initialize cURL
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=UTF-8',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            \Log::error('Curl error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        // Close cURL session
        curl_close($ch);

        // Decode the JSON response
        $responseData = json_decode($response, true);
        return $responseData;
    }

    private function retrievePartQuantity($partNo)
    {
        // The external API URL
        $apiUrl = "http://168.168.1.23/inforLN/api/E-MR/Material_Requistion.php?kitlist=&partno=" . urlencode($partNo);

        // Initialize cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=UTF-8',
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            \Log::error('Curl error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Check if the response is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('JSON decode error: ' . json_last_error_msg());
            return null;
        }

        // Retrieve the quantity from the response data
        foreach ($responseData['data'] ?? [] as $item) {
            if (isset($item['Inv_On_hand'])) {
                return $item['Inv_On_hand'];
            }
        }

        // Return null if no quantity found
        return null;
    }
}

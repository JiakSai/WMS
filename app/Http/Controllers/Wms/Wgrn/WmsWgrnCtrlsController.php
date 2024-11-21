<?php

namespace App\Http\Controllers\Wms\Wgrn;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Wms\Wgrn\WmsWgrnContents;
use App\Models\Wms\Wgrn\WmsWgrnHeaders;
use App\Models\Wms\Whmg\WmsWhmgLocts;
use App\Models\Wms\Whmg\WmsWhmgWhmgs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class WmsWgrnCtrlsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('wms.wgrn.ctrls.index', compact('organisation'));
    }

    /**
    * Show the created shipment.
    */
    public function view(SysOrgaCtrls $organisation, $receipt)
    {
        $grn = WmsWgrnHeaders::where('organisation_id', $organisation->id)
                    ->where('receipt', $receipt)
                    ->first();

        $warehouse = WmsWhmgWhmgs::where('id', $grn->warehouse_id)->first();

        $lines = WmsWgrnContents::where('wgrn_header_id', $grn->id)->get();

        return view('wms.wgrn.ctrls.view', compact('organisation', 'grn', 'warehouse', 'lines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $warehouses = WmsWhmgWhmgs::get();
        return view('wms.wgrn.ctrls.create', compact('organisation', 'warehouses'));
    }

    /**
    * Show the form for creating a new resource.
    */
    public function createContent(SysOrgaCtrls $organisation, WmsWgrnHeaders $header)
    {
        $locations = WmsWhmgLocts::where('warehouse_id', $header->warehouse_id)->get();

        return response()->json(['data' => view('wms.wgrn.ctrls.content.create', compact('organisation', 'header', 'locations'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {   
        $validate = Validator::make($request->all(), [
            'warehouse' => 'required|exists:wms_whmg_whmgs,id',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $receipt = $this->generateReceiptNumber();

        $packingFilePath = null;
        $doFilePath = null;
        $invoiceFilePath = null;

        // Handle Packing Slip File Upload
        if ($request->hasFile('packing_file')) {
            $packingFile = $request->file('packing_file');
            $packingFilename = time() . '_' . uniqid() . '.' . $packingFile->getClientOriginalExtension();
            $packingDirectory = 'grn/packing_slip/';
            $packingFile->move(public_path($packingDirectory), $packingFilename);
            $packingFilePath = $packingDirectory . $packingFilename;
        }

        // Handle Delivery Order (DO) File Upload
        if ($request->hasFile('do_file')) {
            $doFile = $request->file('do_file');
            $doFilename = time() . '_' . uniqid() . '.' . $doFile->getClientOriginalExtension();
            $doDirectory = 'grn/do/';
            $doFile->move(public_path($doDirectory), $doFilename);
            $doFilePath = $doDirectory . $doFilename;
        }

        // Handle Invoice File Upload
        if ($request->hasFile('invoice_file')) {
            $invoiceFile = $request->file('invoice_file');
            $invoiceFilename = time() . '_' . uniqid() . '.' . $invoiceFile->getClientOriginalExtension();
            $invoiceDirectory = 'grn/invoice/';
            $invoiceFile->move(public_path($invoiceDirectory), $invoiceFilename);
            $invoiceFilePath = $invoiceDirectory . $invoiceFilename;
        }

        $grnHeader = WmsWgrnHeaders::create([
            'receipt' => $receipt,
            'warehouse_id' => $request->warehouse,
            'bill' => $request->bill_to,
            'ship' => $request->ship_to,
            'packing_slip' => $request->packing,
            'do' => $request->do,
            'invoice' => $request->invoice,
            'receipt_date' => $request->receipt_date,
            'packing_slip_file' => $packingFilePath,
            'do_file' => $doFilePath,
            'invoice_file' => $invoiceFilePath,
            'organisation_id' => $organisation->id,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $grnHeader->id,
                'receipt' => $grnHeader->receipt,
            ],
        ]);
    }

    /**
    * Generate a unique receipt number in the format GK + Date Today + 9-digit running number.
    */
    private function generateReceiptNumber()
    {
        $prefix = 'GK';
        $date = date('Ymd');

        // Get the latest receipt for today
        $latestReceipt = WmsWgrnHeaders::whereDate('created_at', date('Y-m-d'))
            ->where('receipt', 'like', $prefix . $date . '%')
            ->orderBy('receipt', 'desc')
            ->first();

        if ($latestReceipt) {
            // Extract the last running number and increment it
            $lastNumber = substr($latestReceipt->receipt, -9);
            $newNumber = str_pad(intval($lastNumber) + 1, 9, '0', STR_PAD_LEFT);
        } else {
            // Start from 1
            $newNumber = str_pad(1, 9, '0', STR_PAD_LEFT);
        }

        return $prefix . $date . $newNumber;
    }

    /**
    * Store a newly created content resource in storage.
    */
    public function storeContent(SysOrgaCtrls $organisation, WmsWgrnHeaders $header, Request $request)
    {   
        $validate = Validator::make($request->all(), [
            'serial_number'    => 'required',
            'item_code'        => 'required',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }
        
        $lineItem = WmsWgrnContents::create([
            'wgrn_header_id' => $header->id,
            'serial_number' => $request->serial_number,
            'item_code' => $request->item_code,
            'mpn' => $request->mpn,
            'location' => $request->location,
            'lot' => $request->lot,
            'manufacture_date' => $request->manufacture_date,
            'quantity' => $request->quantity,
            'uom' => $request->uom,
            'organisation_id' => $organisation->id,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        $location = WmsWhmgLocts::where('id', $lineItem->location)->first()->name;

        return response()->json([
            'status' => 2, 
            'message' => 'Line item added successfully', 
            'data' => [
                'id' => $lineItem->id,
                'serial_number' => $lineItem->serial_number,
                'item_code' => $lineItem->item_code,
                'mpn' => $lineItem->mpn,
                'location' => $location,
                'lot' => $lineItem->lot,
                'manufacture_date' => $lineItem->manufacture_date,
                'quantity' => $lineItem->quantity,
                'uom' => $lineItem->uom,
            ]]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = WmsWgrnHeaders::where('organisation_id', $organisation->id);

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = WmsWgrnHeaders::count();
        $filteredRecords = $query->count();

        $header = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $header = $header->map(function ($item) {
                return [
                    'receipt' => $item->receipt,
                    'warehouse' => WmsWhmgWhmgs::where('id', $item->warehouse_id)->first()->name,
                    'packing_slip' => $item->packing_slip,
                    'do' => $item->do,
                    'invoice' => $item->invoice,
                    'receipt_date' => $item->receipt_date,
                    'created_by' => $item->created_by,
                    'updated_by' => $item->updated_by,
                ];
            }); 

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $header
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
        $header = WmsWgrnHeaders::where('receipt', $request->receipt)->first();

        if(!$header){
            return response()->json(['status' => 1, 'message' => 'GRN not found']);
        }

        $header->delete();

        return response()->json(['status' => 2, 'message' => 'GRN deleted successfully']);
    }

    /**
    * Remove the specified resource from content storage.
    */
    public function destroyContent(SysOrgaCtrls $organisation, Request $request)
    {
        $content = WmsWgrnContents::where('id', $request->id)->first();

        if(!$content){
            return response()->json(['status' => 1, 'message' => 'GRN Lines not found']);
        }

        $content->delete();

        return response()->json(['status' => 2, 'message' => 'GRN Lines deleted successfully']);
    }
}

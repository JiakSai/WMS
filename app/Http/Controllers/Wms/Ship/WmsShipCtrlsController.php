<?php

namespace App\Http\Controllers\Wms\Ship;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Wms\Ship\WmsShipContents;
use App\Models\Wms\Ship\WmsShipHeaders;
use App\Models\Wms\Whmg\WmsWhmgLocts;
use App\Models\Wms\Whmg\WmsWhmgWhmgs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WmsShipCtrlsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('wms.ship.ctrls.index', compact('organisation'));
    }

    /**
    * Show the created shipment.
    */
    public function view(SysOrgaCtrls $organisation, $shipment)
    {
        $shipment = WmsShipHeaders::where('organisation_id', $organisation->id)
                    ->where('shipment_no', $shipment)
                    ->first();

        $warehouse = WmsWhmgWhmgs::where('id', $shipment->warehouse_id)->first();

        $lines = WmsShipContents::where('ship_header_id', $shipment->id)->get();

        return view('wms.ship.ctrls.view', compact('organisation', 'shipment', 'warehouse', 'lines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $warehouses = WmsWhmgWhmgs::get();

        return view('wms.ship.ctrls.create', compact('organisation', 'warehouses'));

    }

    /**
    * Show the form for creating a new resource.
    */
    public function createContent(SysOrgaCtrls $organisation, WmsShipHeaders $header)
    {
        $locations = WmsWhmgLocts::where('warehouse_id', $header->warehouse_id)->get();

        return response()->json(['data' => view('wms.ship.ctrls.content.create', compact('organisation', 'header', 'locations'))->render()]);
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

        $shipment = $this->generateShipmentNumber();

        $customsFilePath = null;
        $shipmentSlipFilePath = null;
        $invoiceFilePath = null;

        // Handle Customs Slip File Upload
        if ($request->hasFile('customs_file')) {
            $customsFile = $request->file('customs_file');
            $customsFilename = time() . '_' . uniqid() . '.' . $customsFile->getClientOriginalExtension();
            $customsDirectory = 'shipment/customs_slip/';
            $customsFile->move(public_path($customsDirectory), $customsFilename);
            $customsFilePath = $customsDirectory . $customsFilename;
        }

        // Handle Shipment Slip File Upload
        if ($request->hasFile('shipment_slip_file')) {
            $shipmentSlipFile = $request->file('shipment_slip_file');
            $shipmentSlipFilename = time() . '_' . uniqid() . '.' . $shipmentSlipFile->getClientOriginalExtension();
            $shipmentSlipDirectory = 'shipment/shipment_slip/';
            $shipmentSlipFile->move(public_path($shipmentSlipDirectory), $shipmentSlipFilename);
            $shipmentSlipFilePath = $shipmentSlipDirectory . $shipmentSlipFilename;
        }

        // Handle Invoice File Upload
        if ($request->hasFile('invoice_file')) {
            $invoiceFile = $request->file('invoice_file');
            $invoiceFilename = time() . '_' . uniqid() . '.' . $invoiceFile->getClientOriginalExtension();
            $invoiceDirectory = 'shipment/invoice/';
            $invoiceFile->move(public_path($invoiceDirectory), $invoiceFilename);
            $invoiceFilePath = $invoiceDirectory . $invoiceFilename;
        }

        $shipHeader = WmsShipHeaders::create([
            'shipment_no' => $shipment,
            'warehouse_id' => $request->warehouse,
            'bill' => $request->bill_to,
            'ship' => $request->ship_to,
            'customs_slip' => $request->customs,
            'shipment_slip' => $request->shipment_slip,
            'invoice' => $request->invoice,
            'shipment_date' => $request->shipment_date,
            'customs_slip_file' => $customsFilePath,
            'shipment_slip_file' => $shipmentSlipFilePath,
            'invoice_file' => $invoiceFilePath,
            'organisation_id' => $organisation->id,
            'created_by' => Auth::user()->name,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $shipHeader->id,
                'shipment_no' => $shipHeader->shipment_no,
            ],
        ]);
    }

    /**
    * Generate a unique receipt number in the format GK + Date Today + 9-digit running number.
    */
    private function generateShipmentNumber()
    {
        $prefix = 'SP';
        $date = date('Ymd');

        // Get the latest receipt for today
        $latestShipment = WmsShipHeaders::whereDate('created_at', date('Y-m-d'))
            ->where('shipment_no', 'like', $prefix . $date . '%')
            ->orderBy('shipment_no', 'desc')
            ->first();

        if ($latestShipment) {
            // Extract the last running number and increment it
            $lastNumber = substr($latestShipment->shipment_no, -9);
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
    public function storeContent(SysOrgaCtrls $organisation, WmsShipHeaders $header, Request $request)
    {   
        $validate = Validator::make($request->all(), [
            'serial_number'    => 'required',
            'item_code'        => 'required',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }
        
        $lineItem = WmsShipContents::create([
            'ship_header_id' => $header->id,
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

        $query = WmsShipHeaders::where('organisation_id', $organisation->id);

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = WmsShipHeaders::count();
        $filteredRecords = $query->count();

        $header = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $header = $header->map(function ($item) {
                return [
                    'shipment_no' => $item->shipment_no,
                    'warehouse' => WmsWhmgWhmgs::where('id', $item->warehouse_id)->first()->name,
                    'customs_slip' => $item->customs_slip,
                    'shipment_slip' => $item->shipment_slip,
                    'invoice' => $item->invoice,
                    'shipment_date' => $item->shipment_date,
                    'status' => $item->status,
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
        $header = WmsShipHeaders::where('shipment_no', $request->shipment_no)->first();

        if(!$header){
            return response()->json(['status' => 1, 'message' => 'Shipment not found']);
        }

        $header->delete();

        return response()->json(['status' => 2, 'message' => 'Shipment deleted successfully']);
    }

    public function destroyContent(SysOrgaCtrls $organisation, Request $request)
    {
        $content = WmsShipContents::where('id', $request->id)->first();

        if(!$content){
            return response()->json(['status' => 1, 'message' => 'Shipment Lines not found']);
        }

        $content->delete();

        return response()->json(['status' => 2, 'message' => 'Shipment Lines deleted successfully']);
    }

        /**
     * Remove the specified resource from storage.
     */
    public function approve(SysOrgaCtrls $organisation, Request $request)
    {
        $header = WmsShipHeaders::where('shipment_no', $request->shipment_no)->first();

        if(!$header){
            return response()->json(['status' => 1, 'message' => 'Shipment not found']);
        }

        WmsShipHeaders::where('id', $header->id)->update([
            'status' => 'Approve',
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'Shipment approve successfully']);
    }
}

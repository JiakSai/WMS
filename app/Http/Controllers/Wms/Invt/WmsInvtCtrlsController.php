<?php

namespace App\Http\Controllers\Wms\Invt;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Wms\Invt\WmsInvtCtrls;
use App\Models\Wms\Wgrn\WmsWgrnHeaders;
use App\Models\Wms\Whmg\WmsWhmgLocts;
use App\Models\Wms\Whmg\WmsWhmgWhmgs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WmsInvtCtrlsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('wms.invt.ctrls.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'line_items.*.item_code' => 'required',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $header = WmsWgrnHeaders::find($request->header_id);

        if(!$header){
            return response()->json(['status' => 1, 'message' => 'GRN Header not found']);
        }

        $lineItems = $request->line_items;

        foreach( $lineItems as $lineItem)
        {
            WmsInvtCtrls::create([
                'item_code' => $lineItem['item_code'],
                'warehouse_id' => WmsWhmgWhmgs::where('id', $header->warehouse_id)->first()->id,
                'warehouse_location_id' => WmsWhmgLocts::where('name', $lineItem['location'])->where('warehouse_id', $header->warehouse_id)->first()->id,
                'lot' => $lineItem['lot'],
                'manufacture_date' => $lineItem['manufacture_date'],
                'quantity' => $lineItem['quantity'],
                'uom' => $lineItem['uom'],
                'organisation_id' => $organisation->id,
                'created_by' => Auth::user()->name,
                'updated_by' =>  Auth::user()->name
            ]);
        }

        return response()->json(['status' => '2', 'message' => 'Inventory received successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {       
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = WmsInvtCtrls::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = WmsInvtCtrls::count();
        $filteredRecords = $query->count();

        $data = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $data = $data->map(function ($item) {
                return [
                    'item_code' => $item->item_code,
                    'warehouse' => WmsWhmgWhmgs::where('id', $item->warehouse_id)->first()->name,
                    'warehouse_location' => WmsWhmgLocts::where('id', $item->warehouse_location_id)->first()->name,
                    'lot' => $item->lot,
                    'quantity' => $item->quantity,
                    'uom' => $item->uom,
                    'manufacture_date' => Carbon::parse($item->manufacture_date)->format('Y-m-d'),
                    'created_by' => $item->created_by,
                    'updated_by' => $item->updated_by,
                ];
            }); 

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
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

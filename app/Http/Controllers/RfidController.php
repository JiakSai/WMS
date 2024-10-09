<?php

namespace App\Http\Controllers;

use App\Models\Rfid;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RfidController extends Controller
{
    public function index(Warehouse $warehouse){

        return view('rfidControl', compact('warehouse'));

    }

    public function getRfidData(Warehouse $warehouse, Request $request){

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchRfid = $request->input('rfid', '');

        $query = Rfid::where('warehouse_id', $warehouse->id);

        if (!empty($searchRfid)) {

            $rfidRecord = Rfid::where('warehouse_id', $warehouse->id)
                            ->where('name', $searchRfid)
                            ->first();

            if ($rfidRecord) {
                $query->where('group', $rfidRecord->group);
            }else{
                return response()->json([
                    "draw" => intval($request->input('draw')),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => []
                ]);
            }
        }

        $totalGroups = $query->distinct('group')->count('group');

        $allRfids = $query->orderBy('id', 'DESC')->get()->groupBy('group');

        $rfids = $allRfids->slice($start, $length);

        $data = $rfids->map(function ($group) {
                    return [
                        'from' => $group->last()->name,
                        'to' => $group->first()->name,
                        'created_by' => $group->first()->created_by,
                        'created_at' => $group->first()->created_at->format('Y-m-d H:i:s'),
                    ];
                })->values();

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalGroups,
            "recordsFiltered" => $totalGroups,
            "data" => $data
        ];
    
        return response()->json($json_data);
    }

    public function getRfidChildData(Warehouse $warehouse, Request $request){
        
        $parentId = $request->input('parentId');

        $parentRecord = Rfid::where('name', $parentId)
                            ->where('warehouse_id', $warehouse->id)
                            ->first();

        if (!$parentRecord) {
            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
        }

        $childData = Rfid::where('group', $parentRecord->group)
                        ->where('warehouse_id', $warehouse->id)
                        ->get();

        $formattedChildData = $childData->map(function ($item) {
            return [
                'name' => $item->name,
                'created_by' => $item->created_by,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $childData->count(),
            "recordsFiltered" => $childData->count(),
            "data" => $formattedChildData
        ];

        return response()->json($json_data);
    }

    public function printRfid(Warehouse $warehouse, Request $request){
        
        $quantity = '';
        $created_by = $updated_by = Auth::user()->name;

        $data = view('printRfid', compact('quantity', 'created_by', 'updated_by', 'warehouse'))->render();

        return response()->json(['data' => $data]);
    }

    public function printRfidSubmit(Warehouse $warehouse, Request $request){
        
        $validate = Validator::make( $request->all(), [
            'quantity'=> 'required|integer|min:1',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $quantity = $request->input('quantity');
        $warehouseName = $warehouse->name;
        $date = now()->format('ymd');

        $rfidCodes = [];

        DB::beginTransaction();
        try{
            $lastRfid = Rfid::where('warehouse_id', $warehouse->id)
                            ->orderBy('id', 'desc')
                            ->lockForUpdate()
                            ->first();

            $lastSequence = $lastRfid ? intval(substr($lastRfid->code, -6)) : 0;

            $lastGroup = Rfid::where('warehouse_id', $warehouse->id)
                            ->lockForUpdate()
                            ->max('group');

            $group = $lastGroup ? $lastGroup + 1 : 1;

            for ($i = 1; $i <= $quantity; $i++) {
                do {
                    $sequence = str_pad($lastSequence + $i, 6, '0', STR_PAD_LEFT);
                    $rfidCode = $warehouseName . $date . $sequence;
                    $existingRfid = Rfid::where('name', $rfidCode)
                                ->lockForUpdate()
                                ->first();
                    if ($existingRfid) {
                        $lastSequence++;
                    }
                } while ($existingRfid);
    
                Rfid::create([
                    'name' => $rfidCode,
                    'warehouse_id' => $warehouse->id,
                    'group' => $group,
                    'created_by' => Auth::user()->name,
                    'updated_by' => Auth::user()->name,
                ]);
    
                $rfidCodes[] = $rfidCode;
            }

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status' => 1, 'errors' => ['An error occurred while generating RFID codes.']]);
        }

        $qrCodes = [];

        foreach ($rfidCodes as $code) {
            $qrCodes[] = base64_encode(QrCode::format('svg')->size(64)->generate($code));
        }

        return response()->json([
            'status' => 2,
            'message' => 'RFID codes generated successfully',
            'rfidCodes' => $rfidCodes,
            'qrCodes' => $qrCodes,
        ]);
    }
}

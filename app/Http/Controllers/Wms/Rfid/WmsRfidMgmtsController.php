<?php

namespace App\Http\Controllers\Wms\Rfid;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Wms\Rfid\WmsRfidMgmts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WmsRfidMgmtsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('wms.rfid.mgmts.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        return response()->json(['data' => view('wms.rfid.mgmts.create', compact('organisation'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make( $request->all(), [
            'quantity'=> 'required|integer|min:1',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $quantity = $request->input('quantity');
        $organisationName = $organisation->name;
        $date = now()->format('ymd');

        $rfidCodes = [];

        DB::beginTransaction();
        try{
            $lastRfid = WmsRfidMgmts::where('organisation_id', $organisation->id)
                                    ->orderBy('id', 'desc')
                                    ->lockForUpdate()
                                    ->first();

            $lastSequence = $lastRfid ? intval(substr($lastRfid->code, -6)) : 0;

            $lastGroup = WmsRfidMgmts::where('organisation_id', $organisation->id)
                                     ->lockForUpdate()
                                     ->max('group');

            $group = $lastGroup ? $lastGroup + 1 : 1;

            for ($i = 1; $i <= $quantity; $i++) {
                do {
                    $sequence = str_pad($lastSequence + $i, 6, '0', STR_PAD_LEFT);
                    $rfidCode = $organisationName . $date . $sequence;
                    $existingRfid = WmsRfidMgmts::where('name', $rfidCode)
                                                ->lockForUpdate()
                                                ->first();
                    if ($existingRfid) {
                        $lastSequence++;
                    }
                } while ($existingRfid);
    
                WmsRfidMgmts::create([
                    'name' => $rfidCode,
                    'organisation_id' => $organisation->id,
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

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchRfid = $request->input('rfid', '');

        $query = WmsRfidMgmts::where('organisation_id', $organisation->id);

        if (!empty($searchRfid)) {

            $rfidRecord = WmsRfidMgmts::where('organisation_id', $organisation->id)
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

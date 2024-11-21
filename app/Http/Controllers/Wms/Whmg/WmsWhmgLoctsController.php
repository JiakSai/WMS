<?php

namespace App\Http\Controllers\Wms\Whmg;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Wms\Whmg\WmsWhmgLocts;
use App\Models\Wms\Whmg\WmsWhmgWhmgs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WmsWhmgLoctsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('wms.whmg.locts.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $warehouses = WmsWhmgWhmgs::get();

        return response()->json(['data' => view('wms.whmg.locts.create', compact('organisation', 'warehouses'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' =>'required',
            'warehouse' => 'required'
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        WmsWhmgLocts::create([
            'name' => $request->name,
            'warehouse_id' => $request->warehouse,
            'organisation_id' => $organisation->id,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2 , 'message' => 'Warehouse Location added successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = WmsWhmgLocts::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = WmsWhmgLocts::count();
        $filteredRecords = $query->count();

        $data = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $data = $data->map(function ($item) {

            return [
                'id' => $item->id,
                'warehouse' => WmsWhmgWhmgs::where('id', $item->warehouse_id)->first()->name,
                'name' => $item->name,
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
    public function edit(SysOrgaCtrls $organisation, Request $request)
    {
        $location = WmsWhmgLocts::find($request->id);

        $warehouse = WmsWhmgWhmgs::find($location->warehouse_id);

        if(!$location){
            return response()->json(['status' => 1, 'message' => 'Warehouse Location not found']);
        }

        return response()->json(['data' => view('wms.whmg.locts.edit', compact('location', 'organisation', 'warehouse'))->render()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }
        
        $location = WmsWhmgLocts::find($request->id);

        if(!$location){
            return response()->json(['status' => 1, 'message' => 'Warehouse Location not found']);
        }

        $location->update([
            'name' => $request->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'Warehouse Location updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $location = WmsWhmgLocts::find($request->id);

        if(!$location){
            return response()->json(['status' => 1, 'message' => 'Warehouse Location not found']);
        }

        $location->delete();

        return response()->json(['status' => 2, 'message' => 'Warehouse Location deleted successfully']);
    }
}

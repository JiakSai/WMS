<?php

namespace App\Http\Controllers\Wms\Whmg;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Wms\Whmg\WmsWhmgWhmgs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WmsWhmgWhmgsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('wms.whmg.whmgs.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
  
    public function create(SysOrgaCtrls $organisation)
    {
        return response()->json(['data' => view('wms.whmg.whmgs.create', compact('organisation'))->render()]);
    }
    /**
     * Store a newly created resource in storage.
     */
      public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        WmsWhmgWhmgs::create([            
            'name' => $request->name,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status'=> 2 , 'message' => 'Item Group Added Succesfully !']);
    }
    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = WmsWhmgWhmgs::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = WmsWhmgWhmgs::count();
        $filteredRecords = $query->count();

        $data = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $data = $data->map(function ($warehouse) {

            return [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'description' => $warehouse->description,
                'type' => $warehouse->type,
                'created_by' => $warehouse->created_by,
                'updated_by' => $warehouse->updated_by,
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

    public function edit(SysOrgaCtrls $organisation, Request $request)
    {
        $warehouse = WmsWhmgWhmgs::find($request->id);

        if(!$warehouse)
        {
            return response()->json(['status' => 1, 'message' => 'Warehouse not found']);
        }

        return response()->json(['data' => view('wms.whmg.whmgs.edit', compact('organisation', 'warehouse'))->render()]);
    }

    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $warehouse = WmsWhmgWhmgs::find($request->id);

        if(!$warehouse)
        {
            return response()->json(['status' => 1, 'message' => 'Warehouse not found']);
        }

        WmsWhmgWhmgs::where('id', $warehouse->id)->update([
            'name' => $request->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'Warehouse updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $warehouse = WmsWhmgWhmgs::find($request->id);

        if(!$warehouse){
            return response()->json(['status' => 1, 'message' => 'Warehouse not found']);
        }

        $warehouse->delete();

        return response()->json(['status' => 2, 'message' => 'Warehouse deleted successfully']);
    }
}

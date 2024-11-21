<?php

namespace App\Http\Controllers\Wms\Item;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Wms\Item\WmsItemDatas;
use App\Models\Wms\Item\WmsItemGrpcs;
use App\Models\Wms\Item\WmsItemGrpcsChilds;
use App\Models\Wms\Role\WmsRolePerms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mpdf\Image\Wmf;

class WmsItemDatasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        $tabModuleId = 10;

        // Retrieve the user's role IDs from the session
        $userRoleIds = session('user_role_ids', []);

        // Get the roles that have 'create' permission for this tab module
        $createPermissionRoles = WmsRolePerms::where('tab_module', $tabModuleId)
            ->where('type', 'create')
            ->pluck('role') // This will be a collection of arrays
            ->flatten()
            ->unique()
            ->toArray();

        // Check if the user has 'create' permission
        $hasCreatePermission = !empty(array_intersect($userRoleIds, $createPermissionRoles));

        return view('wms.item.datas.index', compact('organisation', 'hasCreatePermission'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $groupParents = WmsItemGrpcs::get();

        $groupChilds = WmsItemGrpcsChilds::get();

        return response()->json(['data' => view('wms.item.datas.create', compact('organisation', 'groupParents', 'groupChilds'))->render()]);
    } 

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validate->fails())
        {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $item = WmsItemDatas::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'unit_set' => $request->unit_set,
            'inventory_unit' => $request->inventory_unit,
            'weight_unit' => $request->weight_unit,
            'weight' => $request->weight,
            // 'organisation_id' => $organisation->id
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        $groupIds = collect($request->all())
            ->except('weight')
            ->filter(function ($value, $key) {
                // Only get values that are numeric (group IDs)
                return is_numeric($value)&& $value > 0;
            })
            ->values()
            ->toArray();

        // Attach groups if any selected
        if (!empty($groupIds)) {
            $item->itemGroupChilds()->attach($groupIds);
        }

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

        $query = WmsItemDatas::where('organisation_id', $organisation->id);

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = WmsItemDatas::count();
        $filteredRecords = $query->count();

        $data = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $data = $data->map(function ($item) {

            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'type' => $item->type,
                'unit_set' => $item->unit_set,
                'inventory_unit' => $item->inventory_unit,
                'weight_unit' => $item->weight_unit,
                'weight' => $item->weight,
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
        $itemData = WmsItemDatas::find($request->id);

        if(!$itemData)
        {
            return response()->json(['status' => 1, 'message' => 'Item group not found']);
        }

        $groupParents = WmsItemGrpcs::get();

        $groupChilds = WmsItemGrpcsChilds::get();

        $selectedGroupChilds = $itemData->itemGroupChilds->pluck('id')->toArray();

        return response()->json([

            'data' => view('wms.item.datas.edit', compact('organisation', 'itemData', 'groupParents', 'groupChilds', 'selectedGroupChilds'))->render()

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validate->fails())
        {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }
        
        $itemData = WmsItemDatas::find($request->id);

        if(!$itemData)
        {
            return response()->json(['status' => 1, 'message' => 'Item Data not found']);
        }

        WmsItemDatas::where('id', $itemData->id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'unit_set' => $request->unit_set,
            'inventory_unit' => $request->inventory_unit,
            'weight_unit' => $request->weight_unit,
            'weight' => $request->weight,
            // 'organisation_id' => $organisation->id
            'updated_by' => Auth::user()->name,
        ]);

        $groupIds = collect($request->all())
        ->except('weight')
        ->filter(function ($value, $key) {
            // Only get values that are numeric (group IDs)
            return is_numeric($value)&& $value > 0;
        })
        ->values()
        ->toArray();

        // Sync group relationships
        if (!empty($groupIds)) {
            $itemData->itemGroupChilds()->sync($groupIds);
        }

        return response()->json(['status' => 2, 'message' => 'Item Data updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $item = WmsItemDatas::find($request->id);

        if(!$item){
            return response()->json(['status' => 1, 'message' => 'Item not found']);
        }

        $item->delete();

        return response()->json(['status' => 2, 'message' => 'Item deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers\Wms\Item;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Wms\Item\WmsItemGrpcs;
use App\Models\Wms\Item\WmsItemGrpcsChilds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WmsItemGrpcsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('wms.item.grpcs.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        return response()->json(['data' => view('wms.item.grpcs.create', compact('organisation'))->render()]);
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

        WmsItemGrpcs::create([            
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

        $query = WmsItemGrpcs::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = WmsItemGrpcs::count();
        $filteredRecords = $query->count();

        $data = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $data = $data->map(function ($item) {

            return [
                'id' => $item->id,
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
        $itemGroup = WmsItemGrpcs::find($request->id);

        if(!$itemGroup)
        {
            return response()->json(['status' => 1, 'message' => 'Item group not found']);
        }

        return response()->json(['data' => view('wms.item.grpcs.edit', compact('organisation', 'itemGroup'))->render()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $itemGroup = WmsItemGrpcs::find($request->id);

        if(!$itemGroup)
        {
            return response()->json(['status' => 1, 'message' => 'Item group not found']);
        }

        WmsItemGrpcs::where('id', $itemGroup->id)->update([
            'name' => $request->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'Item Group updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $itemGroup = WmsItemGrpcs::find($request->id);

        if(!$itemGroup){
            return response()->json(['status' => 1, 'message' => 'Item Group not found']);
        }

        $itemGroup->delete();

        return response()->json(['status' => 2, 'message' => 'Item Group deleted successfully']);
    }


    /**
    * Display child data
    */
    public function showChildData(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'parentId' => 'required|exists:wms_item_grpcs_childs,parent_id'
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }
    
        // Retrieve child data based on parentId
        $childData = WmsItemGrpcsChilds::where('parent_id', $request->parentId)->get();
    
        // Format the child data
        $formattedChildData = $childData->map(function ($item) {
            return [
                'id' => $item->id,
                'parentId' => $item->parent_id,
                'username' => $item->username,
                'name' => $item->name,
            ];
        });
    
        // Return the formatted child data as JSON response
        return response()->json(['status' => 2, 'data' => $formattedChildData]);
    }

    /**
    * Show the form for creating a new child resource.
    */
    public function createChild(SysOrgaCtrls $organisation, Request $request)
    {
        $parentGroup = WmsItemGrpcs::find($request->id);

        if(!$parentGroup){
            return response()->json(['status' => 1, 'message' => 'Parent group not found']);
        }

        return response()->json(['data' => view('wms.item.grpcs.childs.create', compact('organisation', 'parentGroup'))->render()]);
    }

    /**
    * Store a newly created child resource in storage.   
    */
    public function storeChild(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'parentId' => 'required|exists:wms_item_grpcs,id'
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        WmsItemGrpcsChilds::create([
            'name' => $request->name,
            'parent_id' => $request->parentId,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status'=> 2 , 'message' => 'Item Group Child Added Succesfully !']);
    }

    /**
    * Show the form for editing the specified child resource.
    */
    public function editChild(Request $request)
    {
        $itemGroupChild = WmsItemGrpcsChilds::find($request->id);

        if(!$itemGroupChild)
        {
            return response()->json(['status' => 1, 'message' => 'Item group child not found']);
        }

        return response()->json(['data' => view('wms.item.grpcs.childs.edit', compact('organisation', 'itemGroupChild'))->render()]);
    }

    /**
    * Update the specified child resource in storage.
    */    
    public function updateChild(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'parentId' => 'required|exists:wms_item_grpcs'
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $itemGroup = WmsItemGrpcs::find($request->id);

        if(!$itemGroup)
        {
            return response()->json(['status' => 1, 'message' => 'Item group not found']);
        }

        WmsItemGrpcs::where('id', $itemGroup->id)->update([
            'name' => $request->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'Item Group updated successfully']);
    }

    /**
    * Remove the specified child resource from storage.
    */
    public function destroyChild(SysOrgaCtrls $organisation, Request $request)
    {   
        $childData = WmsItemGrpcsChilds::find($request->childId);

        if(!$childData){
            return response()->json([ 'status' => 1, 'message' => 'Child data not found',]);
        }
         
        $group = WmsItemGrpcs::find($request->parentId);

        if(!$group){
            return response()->json([ 'status' => 1, 'message' => 'Group parent data not found',]);
        }
        
        if ($childData->items) {
            $childData->items()->detach();
        }

        $childData->delete();

        return response()->json(['status' => 2, 'message'=> 'Remove child data successfully!']);

    }
}

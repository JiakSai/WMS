<?php

namespace App\Http\Controllers\Sys\Orga;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SysOrgaCtrlsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('sys.orga.ctrls.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        return response()->json(['data' => view('sys.orga.ctrls.create', compact('organisation'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' =>'required|unique:sys_orga_ctrls,name',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

         SysOrgaCtrls::create([
            'name' => $request->name,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2 , 'message' => 'Organisation added successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = SysOrgaCtrls::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = SysOrgaCtrls::count();
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
    * Display the child data.
    */
    public function showChildData(SysOrgaCtrls $organisation, Request $request)
    {
        $parentId = $request->parentId;

        $childData = SysOrgaCtrls::with('users')
                                 ->where('id', $parentId)
                                 ->first()
                                 ->users;

        $formattedChildData = $childData->map(function ($item) use ($parentId) {
            return [
                'id' => $item->id,
                'parentId' => $parentId,
                'username' => $item->username,
                'name' => $item->name,
            ];
        });

        return response()->json(['data' => $formattedChildData]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SysOrgaCtrls $organisation, Request $request)
    {
        $org = SysOrgaCtrls::find($request->id);

        if(!$org){
            return response()->json(['status' => 1, 'message' => 'Organisation not found']);
        }

        return response()->json(['data' => view('sys.orga.ctrls.edit', compact('organisation', 'org'))->render()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:sys_orga_ctrls,name,'. $request->id
        ]);

        if($validate->fails()){
            $errors = $validate->errors()->all();
            return response()->json(['status'=> 1, 'errors' => $errors]);
        }

        $org = SysOrgaCtrls::find($request->id);

        if(!$org){
            return response()->json(['status' => 1, 'message' => 'Organisation not found']);
        }

        $org->update([
            'name' => $request->name,
            'updated_by' => Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'Organisation updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SysOrgaCtrls $organisation, Request $request)
    {
        $org = SysOrgaCtrls::find($request->id);

        if(!$org){
            return response()->json(['status' => 1, 'message' => 'Organisation not found']);
        }

        $org->delete();

        return response()->json(['status' => 2, 'message' => 'Organisation deleted successfully']);
    }

        /**
     * Remove the user from the organisation
     */
    public function destroyUser(SysOrgaCtrls $organisation, Request $request)
    {

        $user = SysUsrmUsers::find($request->userId);

        $org = SysOrgaCtrls::find($request->orgId);

        if(!$user){
            return response()->json([ 'status' => 1, 'message' => 'User not found',]);
        }

        if(!$org){
            return response()->json([ 'status' => 1, 'message' => 'Organisation not found',]);
        }

        $user->organisations()->detach($org->id);

        return response()->json(['status' => 2, 'message'=> 'Remove user successfully!']);
        
    }
}

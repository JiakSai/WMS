<?php

namespace App\Http\Controllers\Sys\Usrm;

use App\Http\Controllers\Controller;
use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmGrpcs;
use App\Models\Sys\Usrm\SysUsrmRoles;
use App\Models\Sys\Usrm\SysUsrmUsers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SysUsrmUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation)
    {
        return view('sys.usrm.users.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SysOrgaCtrls $organisation)
    {
        $organisations = SysOrgaCtrls::get();

        $groups = SysUsrmGrpcs::get();

        $roles = SysUsrmRoles::where('organisation_id', $organisation->id)->get();

        $mainModules = SysModuMains::get();

        return response()->json(['data' => view('sys.usrm.users.create', compact('organisations', 'groups', 'organisation', 'roles', 'mainModules'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' =>'required|unique:sys_usrm_users,username',
            'name' => 'required|nullable',
            'phone_number' => 'sometimes',
            'email' => ['required', 'email', 'unique:sys_usrm_users,email_address', function ($attribute, $value, $fail) {
                if (!str_contains($value, '@esmtt.com')) {
                    $fail('The '.$attribute.' must be a valid email from @esmtt.com domain.');
                }
            }],
            'telegram_id' => 'sometimes|nullable',
            'default_organisation' => 'required',
            'group' => 'required|exists:sys_usrm_grpcs,id',
            'role' => 'required|exists:sys_usrm_roles,id',
            'permission' => 'required|array|min:1',
        ], [
            'username.required' => 'Employee ID empty is not allowed.',
            'username.unique' => 'Employee ID already exists.',
            'name.required' => 'Full Name empty is not allowed.',
            'email.required' => 'Email empty is not allowed.',
            'email.unique' => 'Email already exists.',
            'default_organisation.required' => 'Please select a default organisation.',
            'group.required' => 'Please select a group.',
            'role.required' => 'Please select a role.'
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }
        
        $user = SysUsrmUsers::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => Hash::make('123456'),
            'phone_number' => $request->phone_number,
            'email_address' => $request->email,
            'telegram_id' => $request->telegram_id, 
            'group' => $request->group,
            'role' => $request->role,
            'default_organisation' => $request->organisation[$request->default_organisation],
            'main_modules_permission' => $request['permission'],
        ]);

        if ($request->has('organisation')) {
            $timestamp = Carbon::now()->format('Y-m-d H:i:s.u');
            $user->organisations()->attach($request->organisation, ['created_at' => $timestamp, 'updated_at' => $timestamp]);
        }

        return response()->json(['status' => 2, 'message' => 'User added successfully!<br>Default password is 123456']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $username = $request->input('empID', '');

        $query = SysUsrmUsers::query();

        if (!empty($username)) {
            $query->where('username', 'LIKE', "%{$username}%");
        }

        $totalRecords = SysUsrmUsers::count();
        $filteredRecords = $query->count();

        $data = $query->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $data = $data->map(function ($item) {

            return [
                'username' => $item->username,
                'name' => $item->name,
                'phone_number' => $item->phone_number,
                'email_address' => $item->email_address,
                'is_active' => $item->is_active ? 'Active' : 'Inactive',
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
        $user = SysUsrmUsers::where('username', $request->id)->first();

        if(!$user){
            return response()->json(['status' => 1, 'message' => 'User not found']);
        }

        $organisations = SysOrgaCtrls::get();

        $selectedOrganisations = $user->organisations()->pluck('id')->toArray();

        $defaultOrganisation = SysOrgaCtrls::find($user->default_organisation);

        $mainModules = SysModuMains::get();

        $groups = SysUsrmGrpcs::get();

        $roles = SysUsrmRoles::where('organisation_id', $organisation->id)->get();

        $selectedPermissionIds = $user->main_modules_permission ?? [];

        $selectedPermissions = SysModuMains::whereIn('id', $selectedPermissionIds)->pluck('id')->toArray();

        return response()->json(['data' => view('sys.usrm.users.edit', compact('user', 'organisations', 'selectedOrganisations', 'defaultOrganisation', 'groups', 'roles', 'organisation', 'mainModules', 'selectedPermissions'))->render()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SysOrgaCtrls $organisation, Request $request)
    {
        $validate = Validator::make($request->all(),[
            'username' => 'required|exists:sys_usrm_users,username',
            'name' => 'required',
            'phone_number' => 'sometimes|nullable',
            'email' => ['required', 'email', 'unique:sys_usrm_users,email_address,'.$request->username.',username', function ($attribute, $value, $fail) {
                if (!str_contains($value, '@esmtt.com')) {
                    $fail('The '.$attribute.' must be a valid email from @esmtt.com domain.');
                }
            }],
            'telegram_id' => 'sometimes|nullable',
            'default_organisation' => 'required',
            'group' => 'required|exists:sys_usrm_grpcs,id',
            'role' => 'required|exists:sys_usrm_roles,id',
            'permission' => 'required|array|min:1',
        ], [
            'username.required' => 'Employee ID empty is not allowed.',
            'username.unique' => 'Employee ID already exists.',
            'name.required' => 'Full Name empty is not allowed.',
            'email.required' => 'Email empty is not allowed.',
            'email.unique' => 'Email already exists.',
            'default_organisation.required' => 'Please select a default organisation.',
            'group.required' => 'Please select a group.',
            'role.required' => 'Please select a role.'
            
        ]);

        if( $validate->fails() ){
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $user = SysUsrmUsers::where('username', $request->username)->first();

        $user->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email_address' => $request->email,
            'telegram_id' => $request->telegram_id,
            'group' => $request->group,
            'role' => $request->role,
            'default_organisation' => $request->organisation[$request->default_organisation],
            'main_modules_permission' => $request['permission'],
        ]);

        // session()->flush();

        if ($request->has('organisation')) {
            $timestamp = Carbon::now()->format('Y-m-d H:i:s.u');
            $user->organisations()->syncWithPivotValues($request->organisation, ['updated_at' => $timestamp]);
        }

        return response()->json(['status' => 2, 'message' => 'User updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function activate(SysOrgaCtrls $organisation, Request $request){

        $user = SysUsrmUsers::where('username', $request->username)->first();

        if(!$user){
            return response()->json(['status' => 1, 'message' => 'User not found']);
        }

        $user->update([
            'is_active' => 1
        ]);

        return response()->json(['status' => 2, 'message' => 'User successfully activated !']);
    }

    public function deactivate(SysOrgaCtrls $organisation, Request $request){

        $user = SysUsrmUsers::where('username', $request->username)->first();

        if(!$user){
            return response()->json(['status' => 1, 'message' => 'User not found']);
        }

        $user->update([
            'is_active' => 0
        ]);

        return response()->json(['status' => 2, 'message' => 'User successfully deactivated !']);
    }
}

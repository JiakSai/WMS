<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Warehouse $warehouse){
        return view('user.index', compact('warehouse'));
    }

    public function userControl(Warehouse $warehouse){
        return view('user.control', compact('warehouse'));
    }

    public function loginPage(){
        
        if(Auth::check()){
            return redirect()->route('home', ['warehouse' => Auth::user()->default_warehouse]);
        }

        return view('login');
    }

    public function resetPassword(){
        return view('resetPassword');
    }

    public function login(Request $request){

        $credentials = $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required'
        ]);

        if ($credentials['password'] === '123456') {
            return redirect()->route('resetPassword');
        }

        $user = User::where('username', $credentials['username'])->first();

        if(!$user->is_active){
            return redirect()->back()->withErrors(['username' => 'This account is inactive']);
        }

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Invalid password'])
                ->withInput($request->except('password'));
        }

        Auth::login($user);
        Auth::logoutOtherDevices($credentials['password']);

        $intendedUrl = session('url.intended');
        
        if ($intendedUrl) {
            $parsedUrl = parse_url($intendedUrl);
            $path = $parsedUrl['path'] ?? '';
            $segments = explode('/', trim($path, '/'));
            
            if (count($segments) > 1 && $segments[0] == 'wms') {
                $warehouseId = $segments[1];
                
                if (!$user->warehouses()->where('id', $warehouseId)->exists()) {
                    session()->forget('url.intended');
                    abort(403);
                }
                
                session(['warehouse_id' => $warehouseId]);
            } else {
                session(['warehouse_id' => $user->default_warehouse]);
            }

            session()->forget('url.intended');
            return redirect($intendedUrl);
        } else {
            session(['warehouse_id' => $user->default_warehouse]);
            return redirect()->route('home', ['warehouse' => $user->default_warehouse]);
        }
    }

    public function resetPasswordSubmit(Request $request){

        $validate = Validator::make($request->all(),[
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);
        
        if($validate->fails()){
            return redirect()->back()->withErrors($validate);
        }

        $user = User::where('username', Auth::user()->username)->first();

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        Auth::login($user);

        if($user->default_warehouse){
    
            session(['warehouse_id' => $user->default_warehouse]);

            return redirect()->route('home', ['warehouse' => $user->default_warehouse]);
        }
    }

    public function resetDefaultPassword(Warehouse $warehouse, Request $request){
        $user = User::where('username', $request->username)->first();

        $user->update([
            'password' => Hash::make('123456')
        ]);

        Log::channel('action_user')->info(json_encode([
            'action' => 'Reset Password',
            // 'user_id' => Auth::user()->Username,
            'data' => $user->toArray()
        ]));

        return response()->json(['status' => 2, 'message' => 'Password reset successfully!<br>Default password is 123456']);
    }

    public function getData(Warehouse $warehouse, Request $request){
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $empID = $request->input('empID', '');

        $query = User::query();

        if (!empty($empID)) {
            $query->where('username', $empID);
        }

        $totalRecords = User::count();
        $filteredRecords = $query->count();

        $users = $query->orderBy('username', 'DESC')
                       ->offset($start)
                       ->limit($length)
                       ->get();
        
        $users = $users->map(function ($user) {
                    return [
                        'username' => $user->username,
                        'name' => $user->name,
                        'phone_number' => $user->phone_number,
                        'email_address' => $user->email_address,
                        'is_active' => $user->is_active ? 'Active' : 'Inactive',
                    ];
                });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $users
        ];

        return response()->json($json_data);
    }

    public function addUser(Warehouse $warehouse, Request $request){
        $username = $name = $phone_number = $email = $telegram_id = $readOnly = '';
        $warehouses = Warehouse::where('is_deleted', 0)
                                ->select('name', 'id')
                                ->get();

        $groups = UserGroup::where('is_deleted', 0)->select('name', 'id')->get();

        $data = view('user.add', compact('username', 'name', 'phone_number', 'email', 'telegram_id', 'warehouses', 'groups', 'readOnly', 'warehouse'))->render();

        return response()->json(['data' => $data]);
    }

    public function editUser(Warehouse $warehouse, Request $request){

        $validate = Validator::make($request->all(), [
            'id' => 'required|exists:users,username'
        ]);
    
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'errors' => $validate->errors()->all()]);
        }
    
        $user = User::where('username', $request->id)->first();
    
        $warehouses = Warehouse::where('is_deleted', 0)
                                ->select('name', 'id')
                                ->get();

        $selectedWarehouses = $user->warehouses()->pluck('id')->toArray();

        $defaultWarehouse = Warehouse::where('id' , $user->default_warehouse)->first();
    
        $groups = UserGroup::where('is_deleted', 0)->select('name', 'id')->get();
        $selectedGroup = $user->group;
    
        $username = $user->username;
        $name = $user->name;
        $phone_number = $user->phone_number;
        $email = $user->email_address;
        $telegram_id = $user->telegram_id;
        $readOnly = 'readonly';
    
        $data = view('user.edit', compact('username', 'name', 'phone_number', 'email', 'telegram_id', 'warehouses', 'selectedWarehouses', 'defaultWarehouse','groups', 'selectedGroup', 'readOnly', 'warehouse'))->render();
    
        return response()->json(['data' => $data]);
    }

    public function addUserSubmit(Warehouse $warehouse, Request $request){

        $validate = Validator::make($request->all(), [
            'username' =>'required|unique:users,username',
            'name' => 'required|nullable',
            'phone_number' => 'sometimes',
            'email' => ['required', 'email', 'unique:users,email_address', function ($attribute, $value, $fail) {
                if (!str_contains($value, '@esmtt.com')) {
                    $fail('The '.$attribute.' must be a valid email from @esmtt.com domain.');
                }
            }],
            'telegram_id' => 'sometimes|nullable',
            'default_warehouse' => 'required',
            'group' => 'required|exists:user_groups,id'
        ], [
            'username.required' => 'Employee ID empty is not allowed.',
            'username.unique' => 'Employee ID already exists.',
            'name.required' => 'Full Name empty is not allowed.',
            'email.required' => 'Email empty is not allowed.',
            'email.unique' => 'Email already exists.',
            'default_warehouse.required' => 'Please select a default warehouse.',
            'group.required' => 'Please select a group.'
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => Hash::make('123456'),
            'phone_number' => $request->phone_number,
            'email_address' => $request->email,
            'telegram_id' => $request->telegram_id, 
            'group' => $request->group,
            'default_warehouse' => $request->warehouse[$request->default_warehouse],
        ]);

        if ($request->has('warehouse')) {
            $timestamp = Carbon::now()->format('Y-m-d H:i:s.u');
            $user->warehouses()->attach($request->warehouse, ['created_at' => $timestamp, 'updated_at' => $timestamp]);
        }

        $message = 'User added successfully!<br>Default password is 123456';

        Log::channel('action_user')->info(json_encode([
            'action' => 'create',
            'user_id' => Auth::user()->username,
            'data' => $user->toArray(),
            'warehouse' => $request->warehouse
        ]));

        return response()->json(['status' => 2, 'message' => $message]);

    }

    public function editUserSubmit(Warehouse $warehouse, Request $request){
        
        $validate = Validator::make($request->all(),[
            'username' => 'required|exists:users,username',
            'name' => 'required',
            'phone_number' => 'sometimes|nullable',
            'email' => ['required', 'email', 'unique:users,email_address,'.$request->username.',username', function ($attribute, $value, $fail) {
                if (!str_contains($value, '@esmtt.com')) {
                    $fail('The '.$attribute.' must be a valid email from @esmtt.com domain.');
                }
            }],
            'telegram_id' => 'sometimes|nullable',
            'default_warehouse' => 'required',
            'group' => 'required|exists:user_groups,id'
        ], [
            'username.required' => 'Employee ID empty is not allowed.',
            'username.unique' => 'Employee ID already exists.',
            'name.required' => 'Full Name empty is not allowed.',
            'email.required' => 'Email empty is not allowed.',
            'email.unique' => 'Email already exists.',
            'default_warehouse.required' => 'Please select a default warehouse.',
            'group.required' => 'Please select a group.'
        ]);

        if( $validate->fails() ){
            $errors = $validate->errors()->all();
            return response()->json(['status' => 1, 'errors' => $errors]);
        }

        $user = User::where('username', $request->username)->first();
        $old_data = $user->toArray();
        $old_warehouses = $user->warehouses()->pluck('id')->toArray();

        $user->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email_address' => $request->email,
            'telegram_id' => $request->telegram_id,
            'group' => $request->group,
            'default_warehouse' => $request->warehouse[$request->default_warehouse],
        ]);

        if ($request->has('warehouse')) {
            $timestamp = Carbon::now()->format('Y-m-d H:i:s.u');
            $user->warehouses()->syncWithPivotValues($request->warehouse, ['updated_at' => $timestamp]);
        }

        $message = 'User updated successfully!';

        Log::channel('action_user')->info(json_encode([
            'action' => 'update',
            'user_id' => Auth::user()->username,
            'old_data' => $old_data,
            'new_data' => $user->toArray(),
            'old_warehouses' => $old_warehouses,
            'new_warehouses' => $request->warehouse
        ]));

        return response()->json(['status' => 2, 'message' => $message]);
    }

    public function activate(Warehouse $warehouse, Request $request){
        $user = User::where('username', $request->username)->first();

        $user->update([
            'is_active' => 1
        ]);

        Log::channel('action_user')->info(json_encode([
            'action' => 'Reset Password',
            // 'user_id' => Auth::user()->Username,
            'data' => $user->toArray()
        ]));

        return response()->json(['status' => 2, 'message' => 'User successfully activated !']);
    }

    public function deactivate(Warehouse $warehouse, Request $request){

        $user = User::where('username', $request->username)->first();

        $user->update([
            'is_active' => 0
        ]);

        Log::channel('action_user')->info(json_encode([
            'action' => 'Reset Password',
            // 'user_id' => Auth::user()->Username,
            'data' => $user->toArray()
        ]));

        return response()->json(['status' => 2, 'message' => 'User successfully deactivated !']);
    }
}

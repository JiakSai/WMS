<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);
        
        if($validate->fails()){
            return redirect()->back()->withErrors($validate);
        }

        $user = SysUsrmUsers::where('username', Auth::user()->username)->first();

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        if($user->default_organisation){
    
            session(['organisation_id' => $user->default_organisation]);

            return redirect()->route('home', ['organisation' => $user->default_organisation]);
        }
    }

    public function resetPasswordDefault(SysOrgaCtrls $organisation ,Request $request)
    {
        $user = SysUsrmUsers::where('username', $request->username)->first();

        $user->update([
            'password' => Hash::make('123456')
        ]);
        
        return response()->json(['status' => 2, 'message' => 'Password reset successfully!<br>Default password is 123456']);
    }
}

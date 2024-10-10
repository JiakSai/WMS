<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Display the login page.
     */
    public function index()
    {
        if(Auth::check()){
            return redirect()->route('home', ['organisation' => Auth::user()->default_organisation]);
        }

        return view('auth.login');
    }

    /**
     * User login function.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|exists:sys_usrm_users,username',
            'password' => 'required'
        ]);

        $user = SysUsrmUsers::where('username', $credentials['username'])->first();

        if(!$user->is_active){
            return redirect()->back()->withErrors(['username' => 'This account is inactive']);
        }

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {

            // Retrieve the authenticated user
            /** @var SysUsrmUsers $user */
            $user = Auth::user();

            if (Hash::check('123456', $user->password)) {
                return redirect()->route('password.reset');
            }
          
            Auth::logoutOtherDevices($credentials['password']);
    
            $intendedUrl = session('url.intended');
            
            if ($intendedUrl) {
                $parsedUrl = parse_url($intendedUrl);
                $path = $parsedUrl['path'] ?? '';
                $segments = explode('/', trim($path, '/'));
                
                if (count($segments) > 1 && $segments[0] == 'wms') {
                    $organisationId = $segments[1];
                    
                    if (!$user->organisations()->where('id', $organisationId)->exists()) {
                        session()->forget('url.intended');
                        abort(403);
                    }
                    
                    session(['organisation_id' => $organisationId]);
                } else {
                    session(['organisation_id' => $user->default_organisation]);
                }
    
                session()->forget('url.intended');
                return redirect($intendedUrl);
            } else {
                session(['organisation_id' => $user->default_organisation]);
                return redirect()->route('home', ['organisation' => $user->default_organisation]);
            }
        } else {
            return redirect()->back()
                ->withErrors(['password' => 'Invalid password'])
                ->withInput($request->except('password'));
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->flush(); 
        return redirect()->route('login');
    }
}

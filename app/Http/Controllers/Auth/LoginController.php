<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Sys\Modu\SysModuTabms;
use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmUsers;
use App\Models\Wms\Role\WmsRoleAsgns;
use App\Models\Wms\Role\WmsRolePerms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

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

            // Retrieve main module permissions
            $mainModuleIds = $user->main_modules_permission ?? [];

            // Store main modules in session
            $request->session()->put('main_modules_permission', $mainModuleIds);

            // // Retrieve tab modules based on main modules
            // $tabModules = SysModuTabms::whereIn('main_group', $mainModuleIds)->get();

            // // Extract tab module IDs
            // $tabModuleIds = $tabModules->pluck('id')->toArray();

            // // Retrieve permissions based on tab modules
            // $permissions = Permission::whereIn('tab_module', $tabModuleIds)->get();

            // // Extract unique role IDs from permissions
            // $roleIds = $permissions->pluck('role')->flatten()->unique()->toArray();

            // // Retrieve roles based on role IDs
            // $roles = Role::whereIn('id', $roleIds)->get();

            // // Extract unique user IDs from roles
            // $userIdsFromRoles = $roles->pluck('user_id')->flatten()->unique()->toArray();

            // // Get the authenticated user's ID
            // $authenticatedUserId = $user->id;

            // if (in_array($authenticatedUserId, $userIdsFromRoles)) {
            //     // Filter roles that include the authenticated user
            //     $userRoles = $roles->filter(function ($role) use ($authenticatedUserId) {
            //         return in_array($authenticatedUserId, $role->user_id);
            //     });

            //     // Extract role IDs
            //     $userRoleIds = $userRoles->pluck('id')->toArray();

            //     // Store role IDs in session
            //     $request->session()->put('user_role_ids', $userRoleIds);

            //     // Optionally, attach roles to the user instance
            //     // (Requires defining a relationship in the SysUsrmUsers model)
            //     $user->roles = $userRoles;
            // } else {
            //     // User does not have the required roles
            //     $request->session()->put('user_role_ids', []);
            // }

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

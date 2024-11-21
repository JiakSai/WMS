<?php

namespace App\Http\Middleware;

use App\Models\Sys\Modu\SysModuSubms;
use App\Models\Sys\Role\SysRoleAuths;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Sys\Usrm\SysUsrmUsers;
use Illuminate\Support\Facades\Session;

class MainModulePermission
{
/**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        // Retrieve sub-module ID from route parameters
        $subModule = $request->route('subModule');

        if (!$subModule) {
            abort(404, 'Sub-module not found.');
        }

        // Get the main module ID associated with this sub-module
        // Ensure you have the correct attribute name
        $mainModuleId = $subModule->group;

        // Retrieve user's main module permissions from the session
        $permissions = Session::get('main_modules_permission', []);

        if (empty($permissions)) {
            abort(403, 'No permissions assigned.');
        }

        // Check if the user's permissions include the main module ID
        if (!in_array($mainModuleId, $permissions)) {
            abort(403, 'Unauthorized access to this sub-module.');
        }

        return $next($request);
    }
}

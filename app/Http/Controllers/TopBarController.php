<?php

namespace App\Http\Controllers;

use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Usrm\SysUsrmUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopBarController extends Controller
{
    public function changeOrganisation()
    {
        $user = Auth::user();

        $organisations = SysUsrmUsers::with('organisations')
                    ->where('id', $user->id)
                    ->first()
                    ->organisations;
        
        $active = session('organisation_id');
        
        $data = view('change-organisation', compact('organisations', 'active'))->render();

        return response()->json(['data' => $data]);
    }

    public function updateOrganisationStatus(Request $request)
    {
        $organisation = SysOrgaCtrls::with('users')->where('name', $request->organisation)->first();

        if($organisation){

            session(['organisation_id' => $organisation->id]);

            $route = route('home', ['organisation' => $organisation->id]);

            return response()->json(['status' => 2, 'route' => $route]);
        }
    }
}

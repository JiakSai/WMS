<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganisationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $organisationId = $request->route('organisation')->id;
        $sessionOrganisationId = session('organisation_id');

        if ($organisationId != $sessionOrganisationId) {
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access. Please log in again.']);
        }

        return $next($request);
    }
}

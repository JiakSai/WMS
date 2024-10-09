<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WarehouseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $warehouseId = $request->route('warehouse')->id;
        $sessionWarehouseId = session('warehouse_id');

        if ($warehouseId != $sessionWarehouseId) {
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access. Please log in again.']);
        }

        return $next($request);
    }
}

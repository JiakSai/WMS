<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'organisation' => App\Http\Middleware\OrganisationMiddleware::class,
            'mainModulePermission' => App\Http\Middleware\MainModulePermission::class,
        ]);

        $middleware->redirectTo(function (Request $request) {
            if (!$request->expectsJson()) {
                $currentSessionId = $request->session()->getId();
                $currentIpAddress = $request->ip();
                
                $sessionData = DB::table('sessions')
                    ->where('id', $currentSessionId)
                    ->first();

                if (Auth::check()) {
                    $currentUserId = Auth::id();
                    
                    if ($sessionData) {
                        $sessionUserId = $sessionData->user_id;
                        $sessionIpAddress = $sessionData->ip_address;

                        if ($sessionUserId != $currentUserId) {
                            Auth::logout();
                            $request->session()->invalidate();
                            session()->flash('alert', 'Your account has been logged in on another device.');
                        } elseif ($sessionIpAddress !== $currentIpAddress) {
                            Auth::logout();
                            $request->session()->invalidate();
                            session()->flash('alert', 'Your session is invalid. Please log in again.');
                        } else {
                            // Session is valid, but the user is trying to access a protected route
                            session()->flash('alert', 'You do not have permission to access this page.');
                        }
                    } else {
                        Auth::logout();
                        $request->session()->invalidate();
                        session()->flash('alert', 'Your session has expired. Please log in again.');
                    }
                } else {
                    session()->flash('alert', 'You must be logged in to access this page.');
                }
                
                return '/'; 
            }
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

<?php

namespace App\Http\Middleware;

use App\Http\Models\UserRoles;
use Closure;
use Illuminate\Support\Facades\Auth;

class ManageServices
{
    public function handle($request, Closure $next)
    {
        if (Auth::user()->cannot('manage_services', UserRoles::class)) {
            abort(401);
        }

        return $next($request);
    }
}

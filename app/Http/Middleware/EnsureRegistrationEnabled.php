<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * P8: gate self-service registration behind the "membership" general setting.
 * When membership is off, the register routes are unavailable and the visitor
 * is redirected to the login page with an explanatory flash message.
 */
class EnsureRegistrationEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! get_general_settings('membership')) {
            return redirect()->route('login')
                ->with('status', trans('default/auth.registration_disabled'));
        }

        return $next($request);
    }
}

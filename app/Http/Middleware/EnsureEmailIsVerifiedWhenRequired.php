<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * P8: enforce email verification on member-only routes, but only while the
 * "email_verification" general setting is enabled. When the toggle is off this
 * is a no-op so existing members are unaffected; when on, an authenticated but
 * unverified member is sent to the verification notice (or refused for JSON
 * requests). Mirrors Laravel's EnsureEmailIsVerified, gated by the setting.
 */
class EnsureEmailIsVerifiedWhenRequired
{
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        if (! get_general_settings('email_verification')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return $request->expectsJson()
                ? abort(409, trans('default/auth.verify_email_required'))
                : redirect()->route($redirectToRoute ?: 'verification.notice')
                    ->with('status', trans('default/auth.verify_email_required'));
        }

        return $next($request);
    }
}

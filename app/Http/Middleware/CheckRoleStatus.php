<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('web')->user() ?? auth('accreditor')->user();

        if ($user && $user instanceof \App\Models\Accreditor) {
            if ($user->expires_at && $user->expires_at < now()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Your access has expired.'], 403);
                }
                return redirect()->route('accreditor.expired');
            }
        }

        if ($user && $user instanceof \App\Models\User && !$user->hasRole(['admin', 'ido_staff'])) {
            // Force college selection if missing (exclude admin/ido_staff)
            if (!$user->college_id) {
                if (!$request->is('onboarding/*') && !$request->is('logout')) {
                    return $request->expectsJson() 
                        ? response()->json(['message' => 'College selection required.'], 403)
                        : redirect()->route('onboarding.college');
                }
            }

            if ($user->role_status === 'pending') {
                if (!$request->is('onboarding/pending') && !$request->is('logout')) {
                    return $request->expectsJson()
                        ? response()->json(['message' => 'Your account is pending approval.'], 403)
                        : redirect()->route('onboarding.pending');
                }
            }
            if ($user->role_status === 'rejected') {
                if (!$request->is('onboarding/rejected') && !$request->is('logout')) {
                    return $request->expectsJson()
                        ? response()->json(['message' => 'Your account access was rejected.'], 403)
                        : redirect()->route('onboarding.rejected');
                }
            }
        }

        return $next($request);
    }
}

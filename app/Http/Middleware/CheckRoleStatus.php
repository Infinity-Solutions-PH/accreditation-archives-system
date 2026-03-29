<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('web')->user();

        if ($user && !$user->hasRole(['admin', 'ido_staff'])) {
            // Force college selection if missing (exclude admin/ido_staff)
            if (!$user->college_id) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'College selection required.'], 403);
                }
                return redirect()->route('onboarding.college');
            }

            if ($user->role_status === 'pending') {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Your account is pending approval.'], 403);
                }
                return redirect()->route('onboarding.pending');
            }
            if ($user->role_status === 'rejected') {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Your account access was rejected.'], 403);
                }
                return redirect()->route('onboarding.rejected');
            }
        }

        return $next($request);
    }
}

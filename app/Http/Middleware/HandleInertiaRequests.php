<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    public function rootView(Request $request): string
    {
        if (auth('accreditor')->check()) {
            return 'layouts.accreditor';
        }

        return 'layouts.app';
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user() ?? auth('accreditor')->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? $user->only(['id', 'name', 'email', 'google_info']) : null,
                'roles' => $user && method_exists($user, 'getRoleNames') ? $user->getRoleNames() : [],
                'is_accreditor' => $user instanceof \App\Models\Accreditor,
            ],
        ];
    }
}

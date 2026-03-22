<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(function (Login $event) {
            $isGoogle = request()->routeIs('google.callback');
            $loginType = $isGoogle ? 'Google System Login' : 'System Login';

            /** @var \Illuminate\Database\Eloquent\Model $user */
            $user = $event->user;

            activity()
                ->causedBy($user)
                ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                ->log($loginType . ' from ' . request()->ip() . ' and ' . request()->userAgent());
        });

        Event::listen(function (Logout $event) {
            /** @var \Illuminate\Database\Eloquent\Model $user */
            $user = $event->user;

            activity()
                ->causedBy($user)
                ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                ->log('System Logout from ' . request()->ip() . ' and ' . request()->userAgent());
        });
    }
}

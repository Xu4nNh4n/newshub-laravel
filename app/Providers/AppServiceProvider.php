<?php

namespace App\Providers;

use App\Http\ViewComposers\DashboardSidebarComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.dashboard', DashboardSidebarComposer::class);

        RateLimiter::for('login', function (Request $request): Limit {
            $email = Str::lower($request->string('email')->toString());

            return Limit::perMinute(5)->by(Str::transliterate($email).'|'.$request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request): Limit {
            $email = Str::lower($request->string('email')->toString());

            return Limit::perHour(5)->by(Str::transliterate($email).'|'.$request->ip());
        });

        RateLimiter::for('verification', fn (Request $request): Limit => Limit::perMinute(3)
            ->by(($request->user()?->getKey() ?? 'guest').'|'.$request->ip()));

        RateLimiter::for('comments', fn (Request $request): Limit => Limit::perMinute(5)
            ->by(($request->user()?->getKey() ?? 'guest').'|'.$request->ip()));

        RateLimiter::for('comment-reports', fn (Request $request): Limit => Limit::perHour(10)
            ->by(($request->user()?->getKey() ?? 'guest').'|'.$request->ip()));
    }
}

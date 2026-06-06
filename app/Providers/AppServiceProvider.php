<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! app()->runningInConsole()) {
            $forwardedProto = request()->headers->get('x-forwarded-proto');
            $host = request()->getHost();

            if ($forwardedProto === 'https' || str_contains($host, 'ngrok')) {
                URL::forceScheme('https');
            }
        }
    }
}

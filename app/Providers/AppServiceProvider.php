<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->ensureWritableRuntimePaths();
    }

    private function ensureWritableRuntimePaths(): void
    {
        $paths = [
            storage_path('framework/views'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('logs'),
            storage_path('app'),
        ];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                @mkdir($path, 0777, true);
            }
        }

        $compiled = storage_path('framework/views');

        if (! is_writable($compiled)) {
            $fallback = sys_get_temp_dir().'/laravel-views';

            if (! is_dir($fallback)) {
                @mkdir($fallback, 0777, true);
            }

            config(['view.compiled' => $fallback]);
        }
    }
}

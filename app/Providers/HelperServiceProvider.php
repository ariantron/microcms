<?php

namespace App\Providers;

use File;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadHelperFiles(app_path('Helpers'));
    }

    private function loadHelperFiles(string $helpersPath): void
    {
        $helperFiles = File::glob($helpersPath . '/*.php');

        foreach ($helperFiles as $file) {
            require_once $file;
        }
    }
}

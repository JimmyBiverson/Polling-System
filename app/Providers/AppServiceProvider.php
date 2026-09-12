<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view): void {
            $view->with('systemBranding', Schema::hasTable('system_settings') ? [
                'name' => SystemSetting::valueFor('system_name', 'Kenya Election Tally'),
                'logo' => SystemSetting::valueFor('system_logo'),
                'tagline' => SystemSetting::valueFor('system_tagline', 'National Polling System — Kakamega County'),
            ] : [
                'name' => 'Kenya Election Tally',
                'logo' => null,
                'tagline' => 'National Polling System — Kakamega County',
            ]);
        });
    }
}

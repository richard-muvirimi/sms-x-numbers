<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->loadMetadataFromComposer();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Load metadata from composer.json into app config
     */
    protected function loadMetadataFromComposer(): void
    {
        if (File::exists(app()->basePath('composer.json'))) {
            $composer = File::json(app()->basePath('composer.json'));

            Config::set('app.author', Arr::get($composer, 'authors.0.name') ?: Config::get('app.author'));
            Config::set('app.description', Arr::get($composer, 'description') ?: Config::get('app.description'));
            Config::set('app.keywords', implode(' ', Arr::get($composer, 'keywords', [])) ?: Config::get('app.keywords'));
            Config::set('app.version', Arr::get($composer, 'version') ?: Config::get('app.version'));
        }
    }
}

<?php

namespace Vikasrinvi\LaravelBugWatcher;

use Illuminate\Support\ServiceProvider;

class LaravelBugWatcherServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-bug-watcher.php' => config_path('laravel-bug-watcher.php'),
        ], 'config');
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/laravel-bug-watcher'),
        ], 'views');
    }
    
    public function register()
    {

        $this->loadViewsFrom(__DIR__.'/views', 'laravel-bug-watcher');
        $this->mergeConfigFrom(
            __DIR__.'/config/laravel-bug-watcher.php',
            'laravel-bug-watcher'
        );

    }
}
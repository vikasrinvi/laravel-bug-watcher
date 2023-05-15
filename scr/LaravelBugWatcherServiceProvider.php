<?php

namespace Vikasrinvi\LaravelBugWatcher;

use Illuminate\Support\ServiceProvider;

class LaravelBugWatcherServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/LaravelBugWatcher.php' => config_path('LaravelBugWatcher.php'),
        ], 'config');
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/LaravelBugWatcher'),
        ], 'views');
    }
    
    public function register()
    {

        $this->loadViewsFrom(__DIR__.'/views', 'laravel-bug-watcher');
        $this->mergeConfigFrom(
            __DIR__.'/config/LaravelBugWatcher.php',
            'laravel-bug-watcher'
        );

    }
}
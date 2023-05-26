<?php

namespace Vikasrinvi\LaravelBugWatcher;

use Illuminate\Support\ServiceProvider;
use Vikasrinvi\LaravelBugWatcher\Interface\BugWatcherInterface;
use Vikasrinvi\LaravelBugWatcher\Interface\ClickupRepository;
use Vikasrinvi\LaravelBugWatcher\Interface\TeamworkRepository;


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

        $this->registerBindings();

        $this->loadViewsFrom(__DIR__.'/views', 'laravel-bug-watcher');
        $this->mergeConfigFrom(
            __DIR__.'/config/laravel-bug-watcher.php',
            'laravel-bug-watcher'
        );

    }
    public function registerBindings()
    {
        $condition = config('laravel-bug-watcher.platform');// Your condition to determine which implementation to use
        
        if ($condition == 'team-work') {
            
           $this->app->bind('Vikasrinvi\LaravelBugWatcher\Interface\BugWatcherInterface::class', 'Vikasrinvi\LaravelBugWatcher\Interface\TeamworkRepository::class');
        } else {
            $this->app->bind('Vikasrinvi\LaravelBugWatcher\Interface\BugWatcherInterface::class', 'Vikasrinvi\LaravelBugWatcher\Interface\ClickupRepository::class');
        }
    }
}
<?php

namespace Vikasrinvi\LaravelBugWatcher\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vikasrinvi\LaravelBugWatcher\LaravelBugWatcherServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelBugWatcherServiceProvider::class,
        ];
    }
}
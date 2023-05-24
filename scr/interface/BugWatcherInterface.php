<?php

namespace Vikasrinvi\LaravelBugWatcher\interface;


interface BugWatcherInterface
{
    public function createTask($exception);

}

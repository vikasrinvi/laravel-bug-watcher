<?php

namespace Vikasrinvi\LaravelBugWatcher\Interface;


interface BugWatcherInterface
{
    public function createTask($exception);

}

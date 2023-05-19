<?php

return [

    'ErrorEmail' => [
        'email' => true,
        'dontEmail' => [],
        'throttle' => true,
        'throttleCacheDriver' => env('CACHE_DRIVER', 'file'),
        'throttleDurationMinutes' => 5,
        'dontThrottle' => [],
        'globalThrottle' => true,
        'globalThrottleLimit' => 20,
        'globalThrottleDurationMinutes' => 30,
        'toEmailAddress' => null,
        'fromEmailAddress' => null,
        'emailSubject' => config('app.name'). " :- Error Occured "
    ],
    'ClickUp' =>[
        'createTask' => true,
        'token' => null,
        'folder_name' =>null,
        'folder_id' => null,
        'list_name' => null,
        'list_id' => null
    ]
];
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
        'token' => env('CLICKUP_ACCESS_TOKEN', null),
        'folder_name' =>env('CLICKUP_FOlDER_NAME', null),
        'folder_id' => env('CLICKUP_FOlDER_ID', null),
        'list_name' => env('CLICKUP_LIST_NAME', null),
        'list_id' => env('CLICKUP_LIST_ID', null)
    ]
];
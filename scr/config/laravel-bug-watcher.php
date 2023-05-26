<?php

return [
    'platform' => 'team-work', // use team-work , click-up
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
        'team_name' => env('CLICKUP_TEAM_NAME', null),
        'folder_name' =>env('CLICKUP_FOlDER_NAME', null),
        'folder_id' => env('CLICKUP_FOlDER_ID', null),
        'list_name' => env('CLICKUP_LIST_NAME', null),
        'list_id' => env('CLICKUP_LIST_ID', null)
    ],
    'TeamWork' =>[
        'createTask' => true,
        'token' => env('TEAMWORK_ACCESS_TOKEN', null),
        'project_id' => 770389,      //integer
        'list_id' => 2587112     //integer
    ]
];
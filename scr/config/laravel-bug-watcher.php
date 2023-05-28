<?php

return [
    'platform' => 'team-work', // use team-work , click-up
    'throttle' => true,
    'throttleCacheDriver' => env('CACHE_DRIVER', 'file'),
    'throttleDurationMinutes' => 5,
    'dontThrottle' => [],
    'globalThrottle' => true,
    'globalThrottleLimit' => 20,
    'globalThrottleDurationMinutes' => 30,
    
    'ErrorEmail' => [
        'email' => true,
        'dontEmail' => [],
        'toEmailAddress' => null,
        'fromEmailAddress' => null,
        'emailSubject' => config('app.name'). " :- Error Occured "
    ],
    'ClickUp' =>[
        'token' => env('CLICKUP_ACCESS_TOKEN', null),
        'team_name' => env('CLICKUP_TEAM_NAME', null),
        'folder_name' =>env('CLICKUP_FOlDER_NAME', null),
        'folder_id' => env('CLICKUP_FOlDER_ID', null),
        'list_name' => env('CLICKUP_LIST_NAME', null),
        'list_id' => env('CLICKUP_LIST_ID', null)
    ],
    'TeamWork' =>[
        'token' => env('TEAMWORK_ACCESS_TOKEN', null),
        'project_id' => env('TEAMWORK_PROJECT_ID', null),      //integer
        'list_id' => env('TEAMWORK_LIST_ID', null)     //integer
    ]
];
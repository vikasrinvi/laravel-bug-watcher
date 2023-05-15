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
        'toEmailAddress' => 'fjwebfw@wejfew.com',
        'fromEmailAddress' => 'fhbewfh@fwje.com',
        'emailSubject' => 'fjebwffw@fbwej.com'
    ]
];
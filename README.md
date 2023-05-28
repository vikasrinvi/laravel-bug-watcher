# laravel-bug-watcher


[![License](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

This is package is made for the developer. 
Whenever there is any bug in the application
- It sends the error message in email to the developer/concern person
- It create a task in the clickup/teamwork.

## Features

- Send Email with the details of the bug.
- Create a task in the clickUp/teamwork with bug report

## Future Scope

- Can be integrated with the AI like chatgpt to which can provide the optimum solution for the bug.
- We can store the error log and create a UI with some route so that all the previous bug on the system can be tracked

## Requirements

- PHP >= 8.1
- Laravel >= 9.0

## Installation

Run the following command
```bash
composer require vikasrinvi/laravel-bug-watcher
 ```
After updating composer, add the service provider to the providers array in config/app.php
> you can skip adding the provider to config/app.php as it will be auto-discovered

```php
Vikasrinvi\LaravelBugWatcher\LaravelBugWatcherServiceProvider::class,
```

Then in app/Exceptions/Handler.php replace
```php
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
```
with
```php
use Vikasrinvi\LaravelBugWatcher\ErrorHandler as ExceptionHandler;
```

## Configuration
To publish the config file

```bash
php artisan vendor:publish --provider="Vikasrinvi\LaravelBugWatcher\LaravelBugWatcherServiceProvider" --tag="config"
```
That will create a config file for you in config/laravel-bug-watcher.php 



Default configuration:
```php

    'platform' => 'click-up', // use team-work , click-up
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
        'team_name' => env('CLICKUP_TEAM_NAME', 'Cubet'),
        'folder_name' =>env('CLICKUP_FOlDER_NAME', 'Laravel Package Test Project'),
        'folder_id' => env('CLICKUP_FOlDER_ID', null),
        'list_name' => env('CLICKUP_LIST_NAME', 'General Tasks'),
        'list_id' => env('CLICKUP_LIST_ID', null)
    ],
    'TeamWork' =>[
        'token' => env('TEAMWORK_ACCESS_TOKEN', null),
        'project_id' => env('TEAMWORK_PROJECT_ID', null),      //integer
        'list_id' => env('TEAMWORK_LIST_ID', null)     //integer
    ]

```


## Basic Usage

#### Basic Config of clickup task

* platform (string) - mention `click-up` for creating task in the clickup.
* token (string) - Token from the clickup
* Team name (string) - Team name is the team in which the project is created we need to get it from the click, it it also the workspace name
* Folder name (string) - This is the project name in the click up
* list name - It is task parent of the task under which task will be created in the clickup
**Important:** There should be a Status under the list call 'BACKLOG' and ther should a label create with the name 'bug.

#### Basic Config of teamwork task

* platform (string) - mention `team-work` for creating task in the teamwork.
* token (string) - Token from the teamwork
* project_id (integer) - Project id is the id of the project need to copy from the url of the project
* list_id (integer) - list id is the list under which the task will be created


#### Basic Config of email

* email (bool) - Enable or disable emailing of errors/exceptions
* dontEmail (array) - This works exactly like laravel's $dontReport variable documented here: [https://laravel.com/docs/10.x/errors](https://laravel.com/docs/10.x/errors)]#the-exception-handler under Ignoring Exceptions By Type. Keep in mind also any exceptions under laravel's $dontReport also will not be emailed
* throttle (bool) - Enable or disable throttling of exception emails. Throttling is only performed if its been determined the exact same exception/error has already been emailed by checking the cache. Errors/Exceptions are determined to be unique by exception class + exception message + exception code
* throttleCacheDriver (string) - The cache driver to use for throttling, by default it uses CACHE_DRIVER from your env file
* throttleDurationMinutes (int) - The duration in minutes of the throttle for example if you put 5 and a BadMethodCallException triggers an email if that same exception is thrown again it will not be emailed until 5 minutes have passed
* dontThrottle (array) - This is the same as dontEmail except provide a list of exceptions you do not wish to throttle ever even if throttling is turned on
* globalThrottle (bool) - Enable or disable whether you want to globally throttle the number of emails you can receive of all exception types by this application
* globalThrottleLimit (int) - The the maximum number of emails you want to receive in a given period.
* throttleDurationMinutes (int) - The duration in minutes of the global throttle for example if you put in 30 and have 10 for your globalThrottleLimit when the first email is sent out a 30 minute timer will commence once you reach the 10 email threshold no more emails will go out for that 30 minute period. 
* toEmailAddress (string|array) - The email(s) to send the exceptions emails to such as the dev team dev@yoursite.com
* fromEmailAddress (string) - The email address these emails should be sent from such as noreply@yoursite.com.

* emailSubject (string) - The subject of email, leave NULL to use default Default Subject: Error Occured config('app.name', 'unknown').' ('.config('app.env', 'unknown').')'

**Note:** the dontReport variable from **app/Exceptions/Handler.php** file will also not be emailed as it's assumed if they are not important enough to log then they also are not important enough to email

**Important:** You must fill out a toEmailAddress and fromEmailAddress or you will not receive emails.

## Advanced Usage
### Changing the view
If you published your view using the command below you will be able to change the look of the exception email
by modifying your view in **resources/views/vendor/laravel-bug-watcher/emailException.blade.php**
follow this to publish the view and the you can modify it
```bash
php artisan vendor:publish --provider="php artisan vendor:publish --provider="Vikasrinvi\LaravelBugWatcher\LaravelBugWatcherServiceProvider" --tag="views"
```


## Important notes
Make sure you have configured your Mail setting in the env so that Mail can be send
```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-user-string
MAIL_PASSWORD=your-password-string
MAIL_ENCRYPTION=null
```


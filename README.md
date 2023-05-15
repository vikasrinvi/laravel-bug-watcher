# laravel-bug-watcher


[![License](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

This is package is made for the developer. It sends the error message in email whenever there is any bug in the application.

## Features

- Send Email with the details of the bug.

## Future Scope

- Can be integrated with the AI like chatgpt to which can provide the optimum solution for the bug.

## Requirements

- PHP >= 8.1
- Laravel >= 9.0

## Installation



Run the following command
```bash
composer require vikasrinvi/laravel-bug-watcher
 ```
After updating composer, add the service provider to the providers array in config/app.php
> If you are in laravel >= 5.5 you can skip adding the provider to config/app.php as it will be auto-discovered

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



To publish the view run the following command (Optional) If you want to modify the view
```bash
php artisan vendor:publish --provider="php artisan vendor:publish --provider="Vikasrinvi\LaravelBugWatcher\LaravelBugWatcherServiceProvider" --tag="views"
```



Default configuration:
```php
'ErrorEmail' => [
    'email' => true,
    'dontEmail' => [],
    'throttle' => false,
    'throttleCacheDriver' => env('CACHE_DRIVER', 'file'),
    'throttleDurationMinutes' => 5,
    'dontThrottle' => [],
    'globalThrottle' => true,
    'globalThrottleLimit' => 20,
    'globalThrottleDurationMinutes' => 30,
    'toEmailAddress' => null,
    'fromEmailAddress' => null,
    'emailSubject' => null
]
```


## Basic Usage
#### Basic Config
Update your config values in **config/laravel-bug-watcher.php**
```php
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
    'toEmailAddress' => 'dev@yoursite.com',
    'fromEmailAddress' => 'noreply@yoursite.com',
    'emailSubject' => null,
]
```

#### Throttling
Both throttling and global throttling are put in place in an attempt to prevent spam to the dev team. Throttling works
by creating a unique cache key made from exception class + exception message + exception code. Its aim is to prevent duplicate
exceptions from being reported via email giving the team time to fix them before they are reported again.

#### Global Throttling
Global throttling is a similar idea except it's put in place to prevent more then a certain number of emails going out 
within a given time period. This should typically only be necessary for an app wide failure ex major portions of the
site are down so many varied types of exceptions are coming in from all directions.

## Advanced Usage
### Changing the view
If you published your view using the command above you will be able to change the look of the exception email
by modifying your view in **resources/views/vendor/laravelEmailExceptions/emailException.blade.php**

### Adding Arbitrary don't email logic
If you need more complicated logic then just checking instanceof against the thrown exception
there is a convenient hook for adding arbitrary logic to decide if an exception should be emailed.


## Gotchas
If you're having trouble getting this working first make sure you have configured your
application to send mail correctly. One of the easiest ways to get mail up and running 
is by signing up for a free account on [mailtrap.io](https://mailtrap.io). Once you've done that you'll have 
to update your .env file with values like these replacing the username and password 
with those listed in your demo inbox
```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-user-string
MAIL_PASSWORD=your-password-string
MAIL_ENCRYPTION=null
```


## License
Copyright (c) 2023 Vikas Rinvi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

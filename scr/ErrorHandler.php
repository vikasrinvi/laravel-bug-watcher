<?php

namespace Vikasrinvi\LaravelBugWatcher;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mail;
use Throwable;
use Vikasrinvi\LaravelBugWatcher\Interface\BugWatcherInterface;
use Vikasrinvi\LaravelBugWatcher\Interface\ClickupRepository;
use Vikasrinvi\LaravelBugWatcher\Interface\TeamworkRepository;
use Vikasrinvi\LaravelBugWatcher\Mail\ErrorMail;
use Illuminate\Contracts\Container\Container;


class ErrorHandler extends ExceptionHandler
{
    /**
     * @var string global throttle cache key
     */
    protected $globalThrottleCacheKey = "email_exception_global";

    /**
     * @var null|string throttle cache key
     */
    protected $throttleCacheKey = null;

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @throws Exception
     */

    protected $clickupRepository;

    public function __construct(Container $container) {
        parent::__construct($container);
        $platform = config('laravel-bug-watcher.platform'); 

        if ($platform =='team-work') {
            $this->clickupRepository = App::make(TeamworkRepository::class);
        } else {
            $this->clickupRepository = App::make(ClickupRepository::class);
        }


    }
    public function report(Throwable $exception)
    {
        if ($this->shouldMail($exception)) {
            // if we passed our validation lets mail the exception
            $this->mailException($exception);
        }
        if($this->shouldCreateTask($exception)){
            $this->clickupRepository->createTask($exception);
        }
        if($this->shouldCreateTeamWorkTask($exception)){
            $this->clickupRepository->createTask($exception);
        }
        // run the parent report (logs exception and all that good stuff)
        $this->callParentReport($exception);
    }

    public function shouldCreateTask($exception)
    {
        if(!config('laravel-bug-watcher.ClickUp.createTask') || !config('laravel-bug-watcher.ClickUp.token') || !config('laravel-bug-watcher.ClickUp.team_name') || !config('laravel-bug-watcher.ClickUp.folder_name') || !config('laravel-bug-watcher.ClickUp.list_name') ||
        // if the exception has already been mailed within the last throttle period
            $this->throttle($exception) ||

            // if we've already sent the maximum amount of emails for the global throttle period
            $this->globalThrottle() || $this->shouldntReport($exception)){
            return false;
        }
        return true;
    }

    public function shouldCreateTeamWorkTask($exception)
    {
        


        if(!config('laravel-bug-watcher.TeamWork.createTask') || !config('laravel-bug-watcher.TeamWork.token') ||  !config('laravel-bug-watcher.TeamWork.folder_id') || !config('laravel-bug-watcher.TeamWork.list_id') ||
        // if the exception has already been mailed within the last throttle period
            $this->throttle($exception) ||

            // if we've already sent the maximum amount of emails for the global throttle period
            $this->globalThrottle() || $this->shouldntReport($exception)){
            return false;
        }
        return true;
    }

    public function creatTask($exception)
    {

        $this->create($exception);
    }

    /**
     * wrapping the parent call to isolate for testing
     *
     * @param Throwable $exception
     * @throws Exception
     */
    protected function callParentReport(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Determine if the exception should be mailed
     *
     * @param Throwable $exception
     * @return bool
     * @throws Exception
     */
    protected function shouldMail(Throwable $exception)
    {
        // if emailing is turned off in the config
        if (config('laravel-bug-watcher.ErrorEmail.email') != true ||

            // if we dont have an email address to mail to
            !config('laravel-bug-watcher.ErrorEmail.toEmailAddress', 'mail.from.address') ||

            // if we dont have an email address to mail from
            !config('laravel-bug-watcher.ErrorEmail.fromEmailAddress', 'mail.from.address') ||

            $this->shouldntReport($exception) ||

            // if the exception is in the don't mail list
            $this->isInDontEmailList($exception) ||

            // if there is any app specific don't email logic
            $this->appSpecificDontEmail($exception) ||

            // if the exception has already been mailed within the last throttle period
            $this->throttle($exception) ||

            // if we've already sent the maximum amount of emails for the global throttle period
            $this->globalThrottle()
        ) {
            // we should not mail this exception
            return false;
        }

        // we made it past all the possible reasons to not email so we should mail this exception
        return true;
    }

    /**
     * app specific dont email logic should go in this function
     *
     * @param Throwable $exception
     * @return bool
     */
    protected function appSpecificDontEmail(Throwable $exception)
    {
        // override this in app/Exceptions/Handler.php if you need more complicated logic
        // then checking instanceof with exception classes
        return false;
    }

    /**
     * mail the exception
     *
     * @param Throwable $exception
     */
    protected function mailException(Throwable $exception)
    {
        $data = [
            'exception' => $exception,
            'toEmail' => config('laravel-bug-watcher.ErrorEmail.toEmailAddress', 'mail.from.address'),
            'fromEmail' => config('laravel-bug-watcher.ErrorEmail.fromEmailAddress', 'mail.from.address'),
            'user' => Auth::user(),
        ];
        Mail::send(new ErrorMail($data));

    }

    /**
     * check if we need to globally throttle the exception
     *
     * @return bool
     * @throws Exception
     */
    protected function globalThrottle()
    {
        // check if global throttling is turned on
        if (config('laravel-bug-watcher.ErrorEmail.globalThrottle') == false) {
            // no need to throttle since global throttling has been disabled
            return false;
        } else {
            // if we have a cache key lets determine if we are over the limit or not
            if (Cache::store(
                config('laravel-bug-watcher.ErrorEmail.throttleCacheDriver')
            )->has($this->globalThrottleCacheKey)
            ) {
                // if we are over the limit return true since this should be throttled
                if (Cache::store(
                    config('laravel-bug-watcher.ErrorEmail.throttleCacheDriver')
                )->get(
                    $this->globalThrottleCacheKey,
                    0
                ) >= config('laravel-bug-watcher.ErrorEmail.globalThrottleLimit')
                ) {
                    return true;
                } else {
                    // else lets increment the cache key and return false since its not time to throttle yet
                    Cache::store(
                        config('laravel-bug-watcher.ErrorEmail.throttleCacheDriver')
                    )->increment($this->globalThrottleCacheKey);

                    return false;
                }
            } else {
                // we didn't find an item in cache lets put it in the cache
                Cache::store(
                    config('laravel-bug-watcher.ErrorEmail.throttleCacheDriver')
                )->put(
                    $this->globalThrottleCacheKey,
                    1,
                    $this->getDateTimeMinutesFromNow(
                        config('laravel-bug-watcher.ErrorEmail.globalThrottleDurationMinutes')
                    )
                );

                // if we're just making the cache key now we are not global throttling yet
                return false;
            }
        }
    }

    /**
     * check if we need to throttle the exception and do the throttling if required
     *
     * @param Throwable $exception
     * @return bool
     * @throws Exception
     */
    protected function throttle(Throwable $exception)
    {
        // if throttling is turned off or its in the dont throttle list we won't throttle this exception
        if (config('laravel-bug-watcher.ErrorEmail.throttle') == false ||
            $this->isInDontThrottleList($exception)
        ) {
            // report that we do not need to throttle
            return false;
        } else {
            // else lets check if its been reported within the last throttle period
            if (Cache::store(
                config('laravel-bug-watcher.ErrorEmail.throttleCacheDriver')
            )->has($this->getThrottleCacheKey($exception))
            ) {
                // if its in the cache we need to throttle
                return true;
            } else {
                // its not in the cache lets add it to the cache
                Cache::store(
                    config('laravel-bug-watcher.ErrorEmail.throttleCacheDriver')
                )->put(
                    $this->getThrottleCacheKey($exception),
                    true,
                    $this->getDateTimeMinutesFromNow(
                        config('laravel-bug-watcher.ErrorEmail.throttleDurationMinutes')
                    )
                );

                // report that we do not need to throttle as its not been reported within the last throttle period
                return false;
            }
        }
    }

    /**
     * get the throttle cache key
     *
     * @param Throwable $exception
     * @return mixed
     */
    protected function getThrottleCacheKey(Throwable $exception)
    {

        // if we haven't already set the cache key lets set it
        if ($this->throttleCacheKey == null) {
            // make up the cache key from a prefix, exception class, exception message, and exception code
            // with all special characters removed
            $this->throttleCacheKey = preg_replace(
                "/[^A-Za-z0-9]/",
                '',
                'laravelEmailException'.get_class($exception).$exception->getMessage().$exception->getCode()
            );
        }

        // return the cache key
        return $this->throttleCacheKey;
    }

    /**
     * check if a given exception matches the class of any in the list
     *
     * @param $list
     * @param Throwable $exception
     * @return bool
     */
    protected function isInList($list, Throwable $exception)
    {
        // check if we actually have a list and its an array
        if ($list && is_array($list)) {
            // if we do lets loop through and check if our exception matches any of the classes
            foreach ($list as $type) {
                if ($exception instanceof $type) {
                    // if we match return true
                    return true;
                }
            }
        }

        // we got to the end there must be no match
        return false;
    }

    /**
     * check if the exception is in the dont throttle list
     *
     * @param Throwable $exception
     * @return bool
     */
    protected function isInDontThrottleList(Throwable $exception)
    {
        $dontThrottleList = config('laravel-bug-watcher.ErrorEmail.dontThrottle');

        return $this->isInList($dontThrottleList, $exception);
    }

    /**
     * check if the exception is in the dont email list
     *
     * @param Throwable $exception
     * @return bool
     */
    protected function isInDontEmailList(Throwable $exception)
    {
        $dontEmailList = config('laravel-bug-watcher.ErrorEmail.dontEmail');

        return $this->isInList($dontEmailList, $exception);
    }

    /**
     * get a datetime minutes from now
     *
     * @param int $minutesToAdd
     * @return \DateTime
     * @throws Exception
     */
    protected function getDateTimeMinutesFromNow($minutesToAdd = 0)
    {
        $now = new \DateTime();

        return $now->modify("+{$minutesToAdd} minutes");
    }
}
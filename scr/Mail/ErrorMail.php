<?php

namespace Vikasrinvi\LaravelBugWatcher\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $default = 'Error Occured '.
                config('app.name', 'unknown').' ('.config('app.env', 'unknown').')';
            $subject = config('laravel-bug-watcher.ErrorEmail.emailSubject') ?: $default;

        return $this->to(config('laravel-bug-watcher.ErrorEmail.toEmailAddress', 'mail.from.address'))->subject($subject)
        ->from(config('laravel-bug-watcher.ErrorEmail.fromEmailAddress', 'mail.from.address'))->subject($subject)
            ->view('laravel-bug-watcher::emailException')->with($this->data);
    }
}

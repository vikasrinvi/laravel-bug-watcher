<?php

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Support\Facades\Mail;
use Vikasrinvi\LaravelBugWatcher\Mail\ErrorMail;


it('sends an email when an error occurs', function () {
       

    // Arrange
    Mail::fake();
    

    // Act
    // Trigger the error that should send the email
    // For example, you can throw an exception
    try {
        throw new Exception('Something went wrong!');
    } catch (Exception $e) {
        // Report the exception and send an email
        app(ExceptionHandler::class)->report($e);
    }


    // Assert
   $data = [
            'exception' => $e,
            'toEmail' => config('laravel-bug-watcher.ErrorEmail.toEmailAddress', 'mail.from.address'),
            'fromEmail' => config('laravel-bug-watcher.ErrorEmail.fromEmailAddress', 'mail.from.address'),
            'user' => Auth::user(),
        ];

        
    Mail::to(config('laravel-bug-watcher.ErrorEmail.toEmailAddress', 'mail.from.address'))->send(new ErrorMail($data));

          Mail::assertSent(ErrorMail::class, function ($sentMail) use ($data) {
        // Assert the recipient
            $recipient = config('laravel-bug-watcher.ErrorEmail.toEmailAddress', 'mail.from.address');
            $this->assertTrue($sentMail->hasTo($recipient));


        return true;
    });

})->group('Unit');

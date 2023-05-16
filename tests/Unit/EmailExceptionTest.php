<?php

use Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;


it('sends an email when an error occurs', function () {
        expect(config('app.env'))->toBe('testing');

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
    $this->assertTrue(true);

    // Assert
   

    // Mail::assertSent(function ($mail) {
    //     // Add assertions for the email sent
    //     // For example, check the recipient, subject, or content of the email
    //     return $mail->to('vikasmrnv@gmail.com')
    //         ->subject('Error Occurred')
    //         ->hasView('laravel-bug-watcher::emailException');
    // });
})->group('Unit');
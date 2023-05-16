<?php

use Mail;


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
    
    Mail::assertSent(function ($mail) {
        // Add assertions for the email sent
        // For example, check the recipient, subject, or content of the email
        return $mail->to('admin@example.com')
            ->subject('Error Occurred')
            ->hasView('laravel-bug-watcher::emailException');
    });
})->group('unit');

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Custom authentication messages
    'login' => [
        'invalid_credentials' => 'Invalid credentials',
        'too_many_attempts' => 'Too many login attempts. Please try again in :seconds seconds.',
        'failed' => 'Login failed. Please try again later.',
    ],

    'register' => [
        'email_already_registered' => 'Email already registered',
        'failed' => 'Registration failed. Please try again later.',
    ],

    'logout' => [
        'success' => 'Successfully logged out',
        'failed' => 'Logout failed. Please try again later.',
    ],

    'password_reset' => [
        'link_sent' => 'Password reset link sent to your email',
        'user_not_found' => "We couldn't find a user with that email address",
        'request_failed' => 'Password reset request failed. Please try again later.',
        'success' => 'Password has been reset successfully',
        'invalid_token' => 'Invalid or expired reset token',
        'failed' => 'Password reset failed. Please try again later.',
        'too_many_attempts' => 'Too many password reset attempts. Please try again later.',
        'incorrect' => 'Your current password is incorrect.'
    ],

    'password_update' => [
        'success' => 'Password updated successfully',
        'failed' => 'Password update failed. Please try again later.',
    ],

    'email_verification' => [
        'already_verified' => 'Your account is already verified',
        'success' => 'Email verification link has been sent to your email',
        'verified' => 'Your account has been successfully verified',
    ],

    'signature' => [
        'invalid_url' => 'Invalid verification link.',
        'invalid_url_or_expired' => 'Invalid or expired verification link.',
        'hash_invalid' => 'Invalid signature link.',
    ]

];

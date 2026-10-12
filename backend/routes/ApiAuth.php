<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->controller(AuthController::class)
    ->group(function () {
        Route::post('login', 'login')->name('api.login');
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('logout', 'logout')->name('logout');
        });

        Route::post('forgot-password', 'forgotPassword')
            ->name('forgot-password');

        Route::middleware([
            'signed'
        ])->group(function () {
            Route::post('reset-password', 'resetPassword')
                ->name('password.reset');
//            Route::post('/email/verify/{user}/{hash}', VerifyEmailController::class)
//                ->name('email.verify');
        });
    });




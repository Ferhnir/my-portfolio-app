<?php

use App\Http\Controllers\TechnologyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
})->name('user');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('technologies', TechnologyController::class);

});

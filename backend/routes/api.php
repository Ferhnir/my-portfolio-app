<?php

use App\Http\Controllers\TechStackCategoryController;
use App\Http\Controllers\TechStackController;
use App\Http\Controllers\TechStackGroupCategoryController;
use App\Http\Controllers\TechStackGroupController;
use App\Http\Controllers\TechStackPlacementController;
use App\Http\Controllers\TechnologyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require 'ApiAuth.php';

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
})->name('user');

Route::get('tech-stack', [TechStackController::class, 'index'])->name('tech-stack.index');

Route::middleware(['auth:sanctum'])->group(function () {
    /* ========================================= */
    /* TECH STACK - GROUP */
    /* ========================================= */
    Route::apiResource('tech-stack-groups', TechStackGroupController::class)
        ->parameters(['tech-stack-groups' => 'techStackGroup']);

    /* ========================================= */
    /* TECH STACK - CATEGORY */
    /* ========================================= */
    Route::apiResource('tech-stack-categories', TechStackCategoryController::class)
        ->parameters(['tech-stack-categories' => 'techStackCategory']);

    /* ========================================= */
    /* TECH STACK - GROUP CATEGORY */
    /* ========================================= */
    Route::controller(TechStackGroupCategoryController::class)->group(function () {

        Route::post('tech-stack-groups/{techStackGroup}/tech-stack-categories/{techStackCategory}', 'store')
            ->name('tech-stack-group-category.store');

        Route::patch('tech-stack-group-category/{techStackGroupCategory}', 'update')
            ->name('tech-stack-group-category.update');

        Route::delete('tech-stack-group-category/{techStackGroupCategory}', 'destroy')
            ->name('tech-stack-group-category.destroy');

    });


//    Route::apiResource('tech-stack-groups.tech-stack-categories', TechStackGroupCategoryController::class)
//        ->only(['store', 'update', 'destroy'])
//        ->parameters([
//            'tech-stack-groups' => 'techStackGroup',
//            'tech-stack-categories' => 'techStackCategory',
//        ]);

    /* ========================================= */
    /* TECH STACK - TECHNOLOGIES */
    /* ========================================= */
    Route::apiResource('technologies', TechnologyController::class);

    /* ========================================= */
    /* TECH STACK - PLACEMENTS */
    /* ========================================= */
    Route::apiResource('tech-stack-placements', TechStackPlacementController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['tech-stack-placements' => 'techStackPlacement']);
});

<?php

use App\Http\Controllers\CaseStudyBlockController;
use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\CaseStudyPointController;
use App\Http\Controllers\CaseStudySectionController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EducationHistoryController;
use App\Http\Controllers\EmploymentController;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\TechStackCategoryController;
use App\Http\Controllers\TechStackController;
use App\Http\Controllers\TechStackGroupCategoryController;
use App\Http\Controllers\TechStackGroupController;
use App\Http\Controllers\TechStackPlacementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require 'ApiAuth.php';

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
})->name('user');

Route::get('tech-stack', [TechStackController::class, 'index'])->name('tech-stack.index');

Route::get('employments', [EmploymentController::class, 'index'])->name('employments.index');
Route::get('education-histories', [EducationHistoryController::class, 'index'])->name('education-histories.index');
Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');

Route::scopeBindings()->group(function () {
    Route::get('case-studies', [CaseStudyController::class, 'index'])->name('case-studies.index');
    Route::get('case-studies/{caseStudy}', [CaseStudyController::class, 'show'])->name('case-studies.show');
    Route::get('case-studies/{caseStudy}/sections', [CaseStudySectionController::class, 'index'])->name('case-studies.sections.index');
    Route::get('case-studies/{caseStudy}/sections/{section}', [CaseStudySectionController::class, 'show'])->name('case-studies.sections.show');
    Route::get('case-studies/{caseStudy}/sections/{section}/blocks', [CaseStudyBlockController::class, 'index'])->name('case-studies.sections.blocks.index');
    Route::get('case-studies/{caseStudy}/sections/{section}/blocks/{block}', [CaseStudyBlockController::class, 'show'])->name('case-studies.sections.blocks.show');
    Route::get('case-studies/{caseStudy}/sections/{section}/blocks/{block}/points', [CaseStudyPointController::class, 'index'])->name('case-studies.sections.blocks.points.index');
    Route::get('case-studies/{caseStudy}/sections/{section}/blocks/{block}/points/{point}', [CaseStudyPointController::class, 'show'])->name('case-studies.sections.blocks.points.show');
});

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

    /* ========================================= */
    /* EMPLOYMENT */
    /* ========================================= */
    Route::apiResource('employments', EmploymentController::class)
        ->except(['index'])
        ->parameters(['employments' => 'employment']);

    /* ========================================= */
    /* EDUCATION HISTORY */
    /* ========================================= */
    Route::apiResource('education-histories', EducationHistoryController::class)
        ->except(['index'])
        ->parameters(['education-histories' => 'educationHistory']);

    /* ========================================= */
    /* COURSES */
    /* ========================================= */
    Route::apiResource('courses', CourseController::class)
        ->except(['index'])
        ->parameters(['courses' => 'course']);

    /* ========================================= */
    /* CERTIFICATES */
    /* ========================================= */
    Route::apiResource('certificates', CertificateController::class)
        ->except(['index'])
        ->parameters(['certificates' => 'certificate']);

    /* ========================================= */
    /* CASE STUDIES */
    /* ========================================= */
    Route::apiResource('case-studies', CaseStudyController::class)
        ->except(['index', 'show'])
        ->parameters(['case-studies' => 'caseStudy']);

    Route::apiResource('case-studies.sections', CaseStudySectionController::class)
        ->except(['index', 'show'])
        ->scoped()
        ->parameters([
            'case-studies' => 'caseStudy',
            'sections' => 'section',
        ]);

    Route::apiResource('case-studies.sections.blocks', CaseStudyBlockController::class)
        ->except(['index', 'show'])
        ->scoped()
        ->parameters([
            'case-studies' => 'caseStudy',
            'sections' => 'section',
            'blocks' => 'block',
        ]);

    Route::apiResource('case-studies.sections.blocks.points', CaseStudyPointController::class)
        ->except(['index', 'show'])
        ->scoped()
        ->parameters([
            'case-studies' => 'caseStudy',
            'sections' => 'section',
            'blocks' => 'block',
            'points' => 'point',
        ]);
});

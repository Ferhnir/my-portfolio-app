<?php

namespace App\Providers;

use App\Models\CaseStudy;
use App\Models\CaseStudySection;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => User::class,
            'case_study' => CaseStudy::class,
            'case_study_section' => CaseStudySection::class,
        ]);
    }
}

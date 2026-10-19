<?php

namespace App\Providers;

use App\Exceptions\ApiException;
use App\Models\CaseStudy;
use App\Models\CaseStudySection;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

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

        RateLimiter::for('contact-messages', function (Request $request) {
            $minutes = (int) config('contact.per_minutes');
            $limits = [
                $this->contactLimit($minutes)->by('ip:'.$request->ip()),
            ];

            $email = Str::lower(trim((string) $request->input('email')));

            if ($email !== '') {
                $limits[] = $this->contactLimit($minutes)->by('email:'.$email);
            }

            return $limits;
        });
    }

    private function contactLimit(int $minutes): Limit
    {
        return Limit::perMinutes($minutes, 1)->response(function () {
            return ApiException::response(
                message: 'Too many messages. Try again in a few minutes.',
                status: Response::HTTP_TOO_MANY_REQUESTS,
            );
        });
    }
}

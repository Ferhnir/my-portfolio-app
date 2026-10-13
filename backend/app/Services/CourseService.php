<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Throwable;

class CourseService
{
    /**
     * @throws Throwable
     */
    public function create(array $attributes): Course
    {
        return DB::transaction(function () use ($attributes): Course {
            $course = Course::query()->create($attributes);

            return $course->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function update(Course $course, array $attributes): Course
    {
        return DB::transaction(function () use ($course, $attributes): Course {
            $course->update($attributes);

            return $course->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(Course $course): void
    {
        DB::transaction(fn () => $course->delete());
    }
}

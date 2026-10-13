<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'name' => 'Example Course',
                'provider' => 'Example Provider',
                'start_date' => '2021-01-15',
                'end_date' => '2021-03-15',
                'description' => null,
                'url' => 'https://example.com/courses/example',
                'sort_order' => 1,
            ],
            [
                'name' => 'Example Workshop',
                'provider' => null,
                'start_date' => '2022-05-01',
                'end_date' => '2022-05-05',
                'description' => null,
                'url' => null,
                'sort_order' => 2,
            ],
        ];

        foreach ($courses as $course) {
            Course::query()->updateOrCreate(
                ['name' => $course['name']],
                $course,
            );
        }
    }
}

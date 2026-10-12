<?php

namespace Database\Seeders;

use App\Models\EducationHistory;
use Illuminate\Database\Seeder;

class EducationHistorySeeder extends Seeder
{
    public function run(): void
    {
        $histories = [
            [
                'school_name' => 'Example University',
                'degree' => 'Bachelor',
                'field_of_study' => 'Computer Science',
                'start_date' => '2016-10-01',
                'end_date' => '2020-06-30',
                'is_current' => false,
                'description' => null,
                'sort_order' => 1,
            ],
            [
                'school_name' => 'Example Bootcamp',
                'degree' => null,
                'field_of_study' => 'Web Development',
                'start_date' => '2021-01-15',
                'end_date' => '2021-06-15',
                'is_current' => false,
                'description' => null,
                'sort_order' => 2,
            ],
        ];

        foreach ($histories as $history) {
            EducationHistory::query()->updateOrCreate(
                ['school_name' => $history['school_name']],
                $history,
            );
        }
    }
}

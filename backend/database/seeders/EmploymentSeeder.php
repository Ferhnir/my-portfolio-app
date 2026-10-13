<?php

namespace Database\Seeders;

use App\Models\Employment;
use Illuminate\Database\Seeder;

class EmploymentSeeder extends Seeder
{
    public function run(): void
    {
        $employments = [
            [
                'company_name' => 'Example Company',
                'job_title' => 'Software Engineer',
                'location' => 'Remote',
                'start_date' => '2020-07-01',
                'end_date' => '2023-03-31',
                'is_current' => false,
                'description' => null,
                'sort_order' => 1,
            ],
            [
                'company_name' => 'Example Studio',
                'job_title' => 'Developer',
                'location' => null,
                'start_date' => '2023-04-01',
                'end_date' => null,
                'is_current' => true,
                'description' => null,
                'sort_order' => 2,
            ],
        ];

        foreach ($employments as $employment) {
            Employment::query()->updateOrCreate(
                ['company_name' => $employment['company_name']],
                $employment,
            );
        }
    }
}

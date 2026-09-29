<?php

namespace Database\Seeders;

use App\Models\TechStackCategory;
use App\Models\TechStackGroup;
use App\Models\TechStackGroupCategory;
use App\Models\TechStackPlacement;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TechStackSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Backend Ecosystem',
                'categories' => [
                    'Core Languages' => ['PHP', 'SQL', 'PYTHON'],
                    'Frameworks' => ['Laravel', 'Symfony', 'Lumen', 'Slim 4'],
                    'Architecture & Packages' => ['RESTful APIs', 'Fortify', 'Sanctum', 'PHPUnit / PEST', 'BREEZE'],
                    'CMS' => ['OCTOBER'],
                ],
            ],
            [
                'name' => 'Frontend Engineering',
                'categories' => [
                    'Core Languages' => ['JavaScript (ES6+)', 'HTML5', 'CSS3'],
                    'Frameworks & Preprocessors' => ['Vue 3 (Composition API)', 'React', 'Tailwind CSS', 'SCSS'],
                    'Build Tools' => ['Vite', 'NPM', 'PostCSS'],
                ],
            ],
            [
                'name' => 'Data Systems & Caching',
                'categories' => [
                    'Relational Databases' => ['MySQL', 'Postgres', 'MariaDB'],
                    'In-Memory Engines' => ['Redis', 'Redis Queues', 'Session Storage'],
                ],
            ],
            [
                'name' => 'DevOps & Systems Control',
                'categories' => [
                    'Virtualization & OS' => ['Docker', 'Docker Compose', 'WSL 2 (Ubuntu)'],
                    'Web Proxies & Engineering Tools' => ['Caddy Server', 'Git / GitHub', 'CI/CD Pipelines'],
                    'AI Coding Efficiency' => ['Claude Code', 'Cursor IDE', 'Prompt Engineering'],
                ],
            ],
        ];

        foreach ($groups as $groupOrder => $groupData) {
            $group = TechStackGroup::query()->updateOrCreate(
                ['slug' => Str::slug($groupData['name'])],
                ['name' => $groupData['name'], 'sort_order' => $groupOrder + 1],
            );

            $categoryOrder = 1;

            foreach ($groupData['categories'] as $categoryName => $technologyNames) {
                $category = TechStackCategory::query()->updateOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    ['name' => $categoryName],
                );

                $groupCategory = TechStackGroupCategory::query()->updateOrCreate(
                    ['group_id' => $group->id, 'category_id' => $category->id],
                    ['sort_order' => $categoryOrder],
                );

                foreach ($technologyNames as $technologyOrder => $technologyName) {
                    $technology = Technology::query()->updateOrCreate(
                        ['slug' => Str::slug($technologyName)],
                        ['name' => $technologyName],
                    );

                    TechStackPlacement::query()->updateOrCreate(
                        [
                            'group_category_id' => $groupCategory->id,
                            'technology_id' => $technology->id,
                        ],
                        ['sort_order' => $technologyOrder + 1],
                    );
                }

                $categoryOrder++;
            }
        }
    }
}

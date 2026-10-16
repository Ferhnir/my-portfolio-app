<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\CaseStudyBlock;
use Illuminate\Database\Seeder;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $caseStudy = CaseStudy::query()->updateOrCreate(
            ['slug' => 'example-project'],
            [
                'title' => 'Example Project',
                'client_name' => 'Example Client',
                'tagline' => 'Internal operational platform',
                'summary' => 'A project management platform that replaced scattered spreadsheets.',
                'introduction' => 'The build brought project tracking, tasks, and reporting into one system.',
                'status' => CaseStudy::STATUS_PUBLISHED,
                'sort_order' => 1,
            ],
        );

        $challenge = $caseStudy->sections()->updateOrCreate(
            ['title' => 'The Challenge'],
            [
                'introduction' => 'The client needed one system from project start through delivery.',
                'sort_order' => 1,
            ],
        );

        $card = $challenge->blocks()->updateOrCreate(
            ['layout' => CaseStudyBlock::LAYOUT_CARD, 'sort_order' => 1],
            [],
        );
        $card->points()->updateOrCreate(
            ['body' => 'Existing work lived in disconnected tools.'],
            ['sort_order' => 1],
        );

        $group = $challenge->blocks()->updateOrCreate(
            ['layout' => CaseStudyBlock::LAYOUT_GROUP, 'sort_order' => 2],
            [],
        );
        $group->points()->updateOrCreate(
            ['body' => 'Project visibility was inconsistent.'],
            ['sort_order' => 1],
        );
        $group->points()->updateOrCreate(
            ['body' => 'A structured internal platform was required.'],
            ['sort_order' => 2],
        );

        $result = $caseStudy->sections()->updateOrCreate(
            ['title' => 'The Result'],
            [
                'introduction' => 'The client now runs projects from one platform.',
                'sort_order' => 2,
            ],
        );
        $resultGroup = $result->blocks()->updateOrCreate(
            ['layout' => CaseStudyBlock::LAYOUT_GROUP, 'sort_order' => 1],
            [],
        );
        $resultGroup->points()->updateOrCreate(
            ['body' => 'Fragmented processes were consolidated.'],
            ['sort_order' => 1],
        );
    }
}

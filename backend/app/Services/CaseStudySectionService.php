<?php

namespace App\Services;

use App\Models\CaseStudy;
use App\Models\CaseStudySection;
use Illuminate\Support\Facades\DB;
use Throwable;

class CaseStudySectionService
{
    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function create(CaseStudy $caseStudy, array $attributes): CaseStudySection
    {
        return DB::transaction(function () use ($caseStudy, $attributes): CaseStudySection {
            $section = $caseStudy->sections()->create($attributes);

            return $section->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function update(CaseStudySection $section, array $attributes): CaseStudySection
    {
        return DB::transaction(function () use ($section, $attributes): CaseStudySection {
            $section->update($attributes);

            return $section->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(CaseStudySection $section): void
    {
        DB::transaction(function () use ($section): void {
            $section->load(['assets', 'blocks.points']);

            foreach ($section->assets as $asset) {
                $asset->delete();
            }

            foreach ($section->blocks as $block) {
                foreach ($block->points as $point) {
                    $point->delete();
                }

                $block->delete();
            }

            $section->delete();
        });
    }
}

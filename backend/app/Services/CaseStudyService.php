<?php

namespace App\Services;

use App\Models\CaseStudy;
use Illuminate\Support\Facades\DB;
use Throwable;

class CaseStudyService
{
    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function create(array $attributes): CaseStudy
    {
        return DB::transaction(function () use ($attributes): CaseStudy {
            $caseStudy = CaseStudy::query()->create($attributes);

            return $caseStudy->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function update(CaseStudy $caseStudy, array $attributes): CaseStudy
    {
        return DB::transaction(function () use ($caseStudy, $attributes): CaseStudy {
            $caseStudy->update($attributes);

            return $caseStudy->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(CaseStudy $caseStudy): void
    {
        DB::transaction(function () use ($caseStudy): void {
            $caseStudy->load(['assets', 'sections.assets', 'sections.blocks.points']);

            foreach ($caseStudy->assets as $asset) {
                $asset->delete();
            }

            foreach ($caseStudy->sections as $section) {
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
            }

            $caseStudy->delete();
        });
    }
}

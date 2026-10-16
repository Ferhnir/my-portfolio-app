<?php

namespace App\Services;

use App\Models\CaseStudyBlock;
use App\Models\CaseStudySection;
use Illuminate\Support\Facades\DB;
use Throwable;

class CaseStudyBlockService
{
    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function create(CaseStudySection $section, array $attributes): CaseStudyBlock
    {
        return DB::transaction(function () use ($section, $attributes): CaseStudyBlock {
            $block = $section->blocks()->create($attributes);

            return $block->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function update(CaseStudyBlock $block, array $attributes): CaseStudyBlock
    {
        return DB::transaction(function () use ($block, $attributes): CaseStudyBlock {
            $block->update($attributes);

            return $block->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(CaseStudyBlock $block): void
    {
        DB::transaction(function () use ($block): void {
            $block->load('points');

            foreach ($block->points as $point) {
                $point->delete();
            }

            $block->delete();
        });
    }
}

<?php

namespace App\Services;

use App\Models\CaseStudyBlock;
use App\Models\CaseStudySection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
            if (
                ($attributes['layout'] ?? null) === CaseStudyBlock::LAYOUT_CARD
                && $block->points()->count() > 1
            ) {
                throw ValidationException::withMessages([
                    'layout' => 'A card can hold one point.',
                ]);
            }

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

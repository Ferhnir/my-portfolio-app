<?php

namespace App\Services;

use App\Models\CaseStudyBlock;
use App\Models\CaseStudyPoint;
use Illuminate\Support\Facades\DB;
use Throwable;

class CaseStudyPointService
{
    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function create(CaseStudyBlock $block, array $attributes): CaseStudyPoint
    {
        return DB::transaction(function () use ($block, $attributes): CaseStudyPoint {
            $point = $block->points()->create($attributes);

            return $point->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function update(CaseStudyPoint $point, array $attributes): CaseStudyPoint
    {
        return DB::transaction(function () use ($point, $attributes): CaseStudyPoint {
            $point->update($attributes);

            return $point->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(CaseStudyPoint $point): void
    {
        DB::transaction(fn () => $point->delete());
    }
}

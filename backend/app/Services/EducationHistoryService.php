<?php

namespace App\Services;

use App\Models\EducationHistory;
use Illuminate\Support\Facades\DB;
use Throwable;

class EducationHistoryService
{
    /**
     * @throws Throwable
     */
    public function create(array $attributes): EducationHistory
    {
        return DB::transaction(function () use ($attributes): EducationHistory {
            $educationHistory = EducationHistory::query()->create($attributes);

            return $educationHistory->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function update(EducationHistory $educationHistory, array $attributes): EducationHistory
    {
        return DB::transaction(function () use ($educationHistory, $attributes): EducationHistory {
            $educationHistory->update($attributes);

            return $educationHistory->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(EducationHistory $educationHistory): void
    {
        DB::transaction(fn () => $educationHistory->delete());
    }
}

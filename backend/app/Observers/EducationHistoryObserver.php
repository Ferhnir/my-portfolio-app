<?php

namespace App\Observers;

use App\Models\EducationHistory;
use App\Services\SortableService;

class EducationHistoryObserver
{
    public function creating(EducationHistory $educationHistory): void
    {
        $column = $educationHistory->getSortableColumnName();
        $order = $educationHistory->getAttribute($column);

        if ($order === null || (int) $order === 0) {
            $max = (int) $educationHistory->newQuery()->max($column);
            $educationHistory->setAttribute($column, $max + 1);
        }
    }

    public function created(EducationHistory $educationHistory): void
    {
        // A new row has no previous order. Syncing it selects the insert branch in SortableService.
        $educationHistory->syncOriginal();

        SortableService::updateOrder($educationHistory);

        $this->reset($educationHistory);
    }

    public function updated(EducationHistory $educationHistory): void
    {
        if ($educationHistory->wasChanged($educationHistory->getSortableColumnName())) {
            SortableService::updateOrder($educationHistory);
        }

        $this->reset($educationHistory);
    }

    public function deleted(EducationHistory $educationHistory): void
    {
        $this->reset($educationHistory);
    }

    private function reset(EducationHistory $educationHistory): void
    {
        SortableService::resetSortOrderKeysValue(
            collection: $educationHistory->newQuery()->orderBy($educationHistory->getSortableColumnName())->get(),
            sortableColumnName: $educationHistory->getSortableColumnName(),
        );
    }
}

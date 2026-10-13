<?php

namespace App\Observers;

use App\Models\CaseStudy;
use App\Services\SortableService;

class CaseStudyObserver
{
    public function creating(CaseStudy $caseStudy): void
    {
        $column = $caseStudy->getSortableColumnName();
        $order = $caseStudy->getAttribute($column);

        if ($order === null || (int) $order === 0) {
            $max = (int) $caseStudy->newQuery()->max($column);
            $caseStudy->setAttribute($column, $max + 1);
        }
    }

    public function created(CaseStudy $caseStudy): void
    {
        // A new row has no previous order. Syncing it selects the insert branch in SortableService.
        $caseStudy->syncOriginal();

        SortableService::updateOrder($caseStudy);

        $this->reset($caseStudy);
    }

    public function updated(CaseStudy $caseStudy): void
    {
        if ($caseStudy->wasChanged($caseStudy->getSortableColumnName())) {
            SortableService::updateOrder($caseStudy);
        }

        $this->reset($caseStudy);
    }

    public function deleted(CaseStudy $caseStudy): void
    {
        $this->reset($caseStudy);
    }

    private function reset(CaseStudy $caseStudy): void
    {
        SortableService::resetSortOrderKeysValue(
            collection: $caseStudy->newQuery()->orderBy($caseStudy->getSortableColumnName())->get(),
            sortableColumnName: $caseStudy->getSortableColumnName(),
        );
    }
}

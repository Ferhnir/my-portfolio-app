<?php

namespace App\Observers;

use App\Models\Employment;
use App\Services\SortableService;

class EmploymentObserver
{
    public function creating(Employment $employment): void
    {
        $column = $employment->getSortableColumnName();
        $order = $employment->getAttribute($column);

        if ($order === null || (int) $order === 0) {
            $max = (int) $employment->newQuery()->max($column);
            $employment->setAttribute($column, $max + 1);
        }
    }

    public function created(Employment $employment): void
    {
        // A new row has no previous order. Syncing it selects the insert branch in SortableService.
        $employment->syncOriginal();

        SortableService::updateOrder($employment);

        $this->reset($employment);
    }

    public function updated(Employment $employment): void
    {
        if ($employment->wasChanged($employment->getSortableColumnName())) {
            SortableService::updateOrder($employment);
        }

        $this->reset($employment);
    }

    public function deleted(Employment $employment): void
    {
        $this->reset($employment);
    }

    private function reset(Employment $employment): void
    {
        SortableService::resetSortOrderKeysValue(
            collection: $employment->newQuery()->orderBy($employment->getSortableColumnName())->get(),
            sortableColumnName: $employment->getSortableColumnName(),
        );
    }
}

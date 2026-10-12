<?php

namespace App\Observers;

use App\Models\Certificate;
use App\Services\SortableService;

class CertificateObserver
{
    public function creating(Certificate $certificate): void
    {
        $column = $certificate->getSortableColumnName();
        $order = $certificate->getAttribute($column);

        if ($order === null || (int) $order === 0) {
            $max = (int) $certificate->newQuery()->max($column);
            $certificate->setAttribute($column, $max + 1);
        }
    }

    public function created(Certificate $certificate): void
    {
        // A new row has no previous order. Syncing it selects the insert branch in SortableService.
        $certificate->syncOriginal();

        SortableService::updateOrder($certificate);

        $this->reset($certificate);
    }

    public function updated(Certificate $certificate): void
    {
        if ($certificate->wasChanged($certificate->getSortableColumnName())) {
            SortableService::updateOrder($certificate);
        }

        $this->reset($certificate);
    }

    public function deleted(Certificate $certificate): void
    {
        $this->reset($certificate);
    }

    private function reset(Certificate $certificate): void
    {
        SortableService::resetSortOrderKeysValue(
            collection: $certificate->newQuery()->orderBy($certificate->getSortableColumnName())->get(),
            sortableColumnName: $certificate->getSortableColumnName(),
        );
    }
}

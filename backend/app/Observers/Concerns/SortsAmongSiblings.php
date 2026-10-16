<?php

namespace App\Observers\Concerns;

use App\Services\SiblingSortService;
use App\Services\SortableService;
use App\SortableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait SortsAmongSiblings
{
    protected function appendWhenMissing(Model&SortableModel $model): void
    {
        $column = $model->getSortableColumnName();
        $order = $model->getAttribute($column);

        if ($order !== null && (int) $order !== 0) {
            return;
        }

        $parentKey = $model->sortParentKey();
        $max = (int) $model->newQuery()
            ->where($parentKey, $model->getAttribute($parentKey))
            ->max($column);

        $model->setAttribute($column, $max + 1);
    }

    protected function insertAmongSiblings(Model&SortableModel $model): void
    {
        $model->syncOriginal();

        SiblingSortService::updateOrder($model);

        $this->resetSiblings($model);
    }

    protected function moveAmongSiblings(Model&SortableModel $model): void
    {
        if ($model->wasChanged($model->getSortableColumnName())) {
            SiblingSortService::updateOrder($model);
        }

        $this->resetSiblings($model);
    }

    protected function resetSiblings(Model&SortableModel $model): void
    {
        $column = $model->getSortableColumnName();
        $siblings = $model->siblings();

        if (! $this->isTrashed($model)) {
            $siblings->push($model);
        }

        SortableService::resetSortOrderKeysValue(
            collection: $siblings,
            sortableColumnName: $column,
        );
    }

    private function isTrashed(Model $model): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($model), true) && $model->trashed();
    }
}

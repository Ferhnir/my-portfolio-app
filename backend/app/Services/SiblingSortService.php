<?php

namespace App\Services;

use App\SortableModel;
use Illuminate\Database\Eloquent\Model;

class SiblingSortService
{
    /**
     * Shift sort positions among the model's siblings. The whole table is left alone.
     */
    public static function updateOrder(Model&SortableModel $model): void
    {
        $column = $model->getSortableColumnName();
        $oldOrder = $model->getOriginal($column);
        $newOrder = $model->getAttribute($column);

        $query = $model->newQuery()->whereKey($model->siblings()->modelKeys());

        match (true) {
            $newOrder > $oldOrder => $query->where($column, '>', $oldOrder)
                ->where($column, '<=', $newOrder)
                ->decrement($column),
            $newOrder < $oldOrder => $query->where($column, '<', $oldOrder)
                ->where($column, '>=', $newOrder)
                ->increment($column),
            default => $query->where($column, '>=', $newOrder)
                ->increment($column),
        };
    }
}

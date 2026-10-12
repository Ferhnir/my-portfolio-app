<?php

namespace App\Services;

use App\Models\TechStackGroupCategory;
use App\SortableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SortableService
{
    public static function updateOrder(
        Model&SortableModel $model
    ): void
    {
        $sortableColumnName = $model->getSortableColumnName();

        $oldOrder = $model->getOriginal($sortableColumnName);
        $newOrder = $model->getAttribute($sortableColumnName);

        $query = $model->newQuery()->whereKeyNot($model->getKey());

        match(true) {
            //MOVE DOWN
            $newOrder > $oldOrder => $query->where($sortableColumnName, '>', $oldOrder)
                ->where($sortableColumnName, '<=', $newOrder)
                ->decrement($sortableColumnName),
            //MOVE UP
            $newOrder < $oldOrder => $query->where($sortableColumnName, '<', $oldOrder)
                ->where($sortableColumnName, '>=', $newOrder)
                ->increment($sortableColumnName),

            //MOVE IN NEW ELEMENT
            default => $query->where($sortableColumnName, '>=', $newOrder)
                ->increment($sortableColumnName),
        };
    }

    public static function resetSortOrderKeysValue(
        Collection $collection,
        string $sortableColumnName
    ): void
    {
        $ordered = $collection->sortBy($sortableColumnName)->values();

        $inOrder = $ordered->every(
            fn (Model $category, int $key): bool => (int) $category->getAttribute($sortableColumnName) === $key + 1,
        );

        if ($inOrder) {
            return;
        }

        foreach ($ordered as $key => $category) {
            $category->updateQuietly([
                $sortableColumnName => $key + 1,
            ]);
        }
    }
}

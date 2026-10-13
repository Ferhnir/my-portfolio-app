<?php

namespace App\Observers;

use App\Models\Course;
use App\Services\SortableService;

class CourseObserver
{
    public function creating(Course $course): void
    {
        $column = $course->getSortableColumnName();
        $order = $course->getAttribute($column);

        if ($order === null || (int) $order === 0) {
            $max = (int) $course->newQuery()->max($column);
            $course->setAttribute($column, $max + 1);
        }
    }

    public function created(Course $course): void
    {
        // A new row has no previous order. Syncing it selects the insert branch in SortableService.
        $course->syncOriginal();

        SortableService::updateOrder($course);

        $this->reset($course);
    }

    public function updated(Course $course): void
    {
        if ($course->wasChanged($course->getSortableColumnName())) {
            SortableService::updateOrder($course);
        }

        $this->reset($course);
    }

    public function deleted(Course $course): void
    {
        $this->reset($course);
    }

    private function reset(Course $course): void
    {
        SortableService::resetSortOrderKeysValue(
            collection: $course->newQuery()->orderBy($course->getSortableColumnName())->get(),
            sortableColumnName: $course->getSortableColumnName(),
        );
    }
}

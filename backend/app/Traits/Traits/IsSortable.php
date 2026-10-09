<?php

namespace App\Traits\Traits;

use Illuminate\Database\Eloquent\Collection;

trait IsSortable
{
    public function getSortableColumnName(): string
    {
        return self::SORTABLE_COLUMN;
    }

    public function siblings(): Collection
    {
        return self::newQuery()
            ->where(self::LINKABLE_MODEL_PARENT_KEY, $this->getAttribute(self::LINKABLE_MODEL_PARENT_KEY))
            ->whereKeyNot(self::getKey())
            ->orderBy(self::SORTABLE_COLUMN)
            ->get();
    }
}

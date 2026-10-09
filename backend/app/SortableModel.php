<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;

interface SortableModel
{
    public function getSortableColumnName(): string;

    public function siblings(): Collection;
}

<?php

namespace App\Observers;

use App\Models\Asset;
use App\Observers\Concerns\SortsAmongSiblings;
use App\SortableModel;
use Illuminate\Database\Eloquent\Model;

class AssetObserver
{
    use SortsAmongSiblings;

    public function creating(Asset $asset): void
    {
        $this->appendWhenMissing($asset);
    }

    public function created(Asset $asset): void
    {
        $this->insertAmongSiblings($asset);
    }

    public function updated(Asset $asset): void
    {
        $this->moveAmongSiblings($asset);
    }

    public function deleted(Asset $asset): void
    {
        $this->resetSiblings($asset);
    }

    protected function appendWhenMissing(Model&SortableModel $model): void
    {
        $column = $model->getSortableColumnName();
        $order = $model->getAttribute($column);

        if ($order !== null && (int) $order !== 0) {
            return;
        }

        $max = (int) $model->newQuery()
            ->where('assetable_type', $model->getAttribute('assetable_type'))
            ->where('assetable_id', $model->getAttribute('assetable_id'))
            ->where('collection', $model->getAttribute('collection'))
            ->max($column);

        $model->setAttribute($column, $max + 1);
    }
}

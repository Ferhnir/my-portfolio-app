<?php

namespace App\Observers;

use App\Models\TechStackGroup;
use App\Models\TechStackGroupCategory;
use App\Services\SortableService;
use App\Services\TechStackGroupCategoryService;

class TechStackGroupCategoryObserver
{
    /**
     * Handle the TechStackGroupCategory "created" event.
     */
    public function created(TechStackGroupCategory $techStackGroupCategory): void
    {
        $this->updateSortOrder($techStackGroupCategory);
    }

    /**
     * Handle the TechStackGroupCategory "updating" event.
     */
    public function updating(TechStackGroupCategory $techStackGroupCategory): void
    {
    }

    /**
     * Handle the TechStackGroupCategory "updated" event.
     */
    public function updated(TechStackGroupCategory $techStackGroupCategory): void {
        $this->updateSortOrder($techStackGroupCategory);
    }

    public function deleting(TechStackGroupCategory $techStackGroupCategory): void
    {
    }

    /**
     * Handle the TechStackGroupCategory "deleted" event.
     */
    public function deleted(TechStackGroupCategory $techStackGroupCategory): void
    {
        $this->updateSortOrder($techStackGroupCategory);
    }

    private function updateSortOrder(TechStackGroupCategory $techStackGroupCategory): void
    {
        $parentModelKeyName = $techStackGroupCategory::LINKABLE_MODEL_PARENT_KEY;
        $sortOrderKeyName = $techStackGroupCategory::SORTABLE_COLUMN;

        //MODEL CREATED (LINKED) OR ORDER CHANGED
        if (
            is_null($techStackGroupCategory->getAttribute(key: $sortOrderKeyName)) ||
            $techStackGroupCategory->wasChanged(attributes: $sortOrderKeyName)
        ) {
            SortableService::updateOrder(model: $techStackGroupCategory);
        }

        //MODEL RE-ASSIGNED TO DIFFERENT PARENT (GROUP) - RESETTING OLD ONE
        if ($techStackGroupCategory->wasChanged(attributes: $parentModelKeyName)) {

            $originalParentGroupID = (int) $techStackGroupCategory->getOriginal(key: $parentModelKeyName);
            $originalGroup = TechStackGroup::query()->find($originalParentGroupID);

            if ($originalGroup) {
                SortableService::resetSortOrderKeysValue(
                    collection: $originalGroup->parent?->techStackGroupLinks ?? collect(),
                    sortableColumnName: $techStackGroupCategory::SORTABLE_COLUMN
                );
            }

        }

        //RESET SORT ODER VALUES (TO REMOVE GAPS IF EXISTS)
        SortableService::resetSortOrderKeysValue(
            collection: $techStackGroupCategory->parent?->techStackGroupLinks ?? collect(),
            sortableColumnName: $techStackGroupCategory::SORTABLE_COLUMN
        );
    }
}

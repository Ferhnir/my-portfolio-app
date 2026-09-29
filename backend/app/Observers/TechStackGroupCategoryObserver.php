<?php

namespace App\Observers;

use App\Models\TechStackGroupCategory;

class TechStackGroupCategoryObserver
{
    /**
     * Handle the TechStackGroupCategory "created" event.
     */
    public function created(TechStackGroupCategory $techStackGroupCategory): void
    {
        //
    }

    /**
     * Handle the TechStackGroupCategory "updated" event.
     */
    public function updated(TechStackGroupCategory $techStackGroupCategory): void
    {
    }

    /**
     * Handle the TechStackGroupCategory "deleted" event.
     */
    public function deleted(TechStackGroupCategory $techStackGroupCategory): void
    {
        //
    }

    /**
     * Handle the TechStackGroupCategory "restored" event.
     */
    public function restored(TechStackGroupCategory $techStackGroupCategory): void
    {
        //
    }

    /**
     * Handle the TechStackGroupCategory "force deleted" event.
     */
    public function forceDeleted(TechStackGroupCategory $techStackGroupCategory): void
    {
        //
    }
}

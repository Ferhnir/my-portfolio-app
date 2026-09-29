<?php

namespace App\Observers;

use App\Models\TechStackGroup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TechStackGroupObserver
{
    public function creating(TechStackGroup $techStackGroup): void
    {
        $techStackGroup->setAttribute('slug', Str::slug($techStackGroup->getAttribute('name')));
    }

    public function created(TechStackGroup $techStackGroup): void
    {
        Cache::forget('tech-stack.index');
    }

    public function updating(TechStackGroup $techStackGroup): void
    {
        if ($techStackGroup->isDirty('name')) {
            $techStackGroup->setAttribute('slug', Str::slug($techStackGroup->getAttribute('name')));
        }
    }

    public function updated(TechStackGroup $techStackGroup): void
    {
        Cache::forget('tech-stack.index');
    }

    public function deleted(TechStackGroup $techStackGroup): void
    {
        Cache::forget('tech-stack.index');
    }
}

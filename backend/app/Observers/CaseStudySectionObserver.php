<?php

namespace App\Observers;

use App\Models\CaseStudySection;
use App\Observers\Concerns\SortsAmongSiblings;

class CaseStudySectionObserver
{
    use SortsAmongSiblings;

    public function creating(CaseStudySection $section): void
    {
        $this->appendWhenMissing($section);
    }

    public function created(CaseStudySection $section): void
    {
        $this->insertAmongSiblings($section);
    }

    public function updated(CaseStudySection $section): void
    {
        $this->moveAmongSiblings($section);
    }

    public function deleted(CaseStudySection $section): void
    {
        $this->resetSiblings($section);
    }
}

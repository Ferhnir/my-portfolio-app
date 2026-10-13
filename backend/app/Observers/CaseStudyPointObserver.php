<?php

namespace App\Observers;

use App\Models\CaseStudyPoint;
use App\Observers\Concerns\SortsAmongSiblings;

class CaseStudyPointObserver
{
    use SortsAmongSiblings;

    public function creating(CaseStudyPoint $point): void
    {
        $this->appendWhenMissing($point);
    }

    public function created(CaseStudyPoint $point): void
    {
        $this->insertAmongSiblings($point);
    }

    public function updated(CaseStudyPoint $point): void
    {
        $this->moveAmongSiblings($point);
    }

    public function deleted(CaseStudyPoint $point): void
    {
        $this->resetSiblings($point);
    }
}

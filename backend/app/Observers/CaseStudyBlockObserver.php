<?php

namespace App\Observers;

use App\Models\CaseStudyBlock;
use App\Observers\Concerns\SortsAmongSiblings;

class CaseStudyBlockObserver
{
    use SortsAmongSiblings;

    public function creating(CaseStudyBlock $block): void
    {
        $this->appendWhenMissing($block);
    }

    public function created(CaseStudyBlock $block): void
    {
        $this->insertAmongSiblings($block);
    }

    public function updated(CaseStudyBlock $block): void
    {
        $this->moveAmongSiblings($block);
    }

    public function deleted(CaseStudyBlock $block): void
    {
        $this->resetSiblings($block);
    }
}

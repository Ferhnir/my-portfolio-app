<?php

namespace App\Http\Controllers\Concerns;

use App\Models\CaseStudy;

trait GuardsCaseStudyVisibility
{
    protected function guardCaseStudy(CaseStudy $caseStudy): void
    {
        if ($caseStudy->status === CaseStudy::STATUS_PUBLISHED) {
            return;
        }

        abort_unless(auth('sanctum')->user() !== null, 404);
    }
}

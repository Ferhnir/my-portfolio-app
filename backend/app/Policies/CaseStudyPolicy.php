<?php

namespace App\Policies;

use App\Models\CaseStudy;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CaseStudyPolicy
{
    public function viewDrafts(?User $user): bool
    {
        return $this->allows($user, 'case-study.view-draft');
    }

    public function view(?User $user, CaseStudy $caseStudy): Response
    {
        if ($caseStudy->status === CaseStudy::STATUS_PUBLISHED || $this->viewDrafts($user)) {
            return Response::allow();
        }

        return Response::denyAsNotFound();
    }

    public function create(?User $user): bool
    {
        return $this->allows($user, 'case-study.create');
    }

    public function update(?User $user, CaseStudy $caseStudy): bool
    {
        return $this->allows($user, 'case-study.update');
    }

    public function delete(?User $user, CaseStudy $caseStudy): bool
    {
        return $this->allows($user, 'case-study.delete');
    }

    /**
     * Any logged-in user is allowed. Replace the body with a Spatie permission check.
     */
    private function allows(?User $user, string $permission): bool
    {
        return $user !== null && $permission !== '';
    }
}

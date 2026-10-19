<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    public function viewAny(?User $user): bool
    {
        return $this->allows($user);
    }

    public function view(?User $user, ContactMessage $contactMessage): bool
    {
        return $this->allows($user);
    }

    public function create(?User $user): bool
    {
        return true;
    }

    /**
     * Any logged-in user is allowed. Replace the body with a Spatie permission check.
     */
    private function allows(?User $user): bool
    {
        return $user !== null;
    }
}

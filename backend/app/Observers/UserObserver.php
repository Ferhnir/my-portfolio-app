<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;

class UserObserver
{
    #[NoReturn]
    public function creating(User $user): void
    {
        if (empty($user->password)) {
            $user->setAttribute('password', Str::random(32));
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}

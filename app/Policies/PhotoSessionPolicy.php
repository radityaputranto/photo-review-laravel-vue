<?php

namespace App\Policies;

use App\Models\PhotoSession;
use App\Models\User;

class PhotoSessionPolicy
{
    public function view(User $user, PhotoSession $session): bool
    {
        if ($user->isAdmin()) return true;
        return $user->id === $session->customer_id;
    }

    public function select(User $user, PhotoSession $session): bool
    {
        return $user->id === $session->customer_id
            && $session->status === 'active'
            && is_null($session->submitted_at);
    }
}

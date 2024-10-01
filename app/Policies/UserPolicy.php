<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(User $user, User $profileUser)
    {
        
    }

    public function update(User $user, User $profileUser)
    {
        
    }

}

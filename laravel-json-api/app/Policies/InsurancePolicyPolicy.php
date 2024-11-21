<?php

namespace App\Policies;

use App\Models\User;

class InsurancePolicyPolicy
{
    public function execute(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'User']);
    }


    public function delete(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}

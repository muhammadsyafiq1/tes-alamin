<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function aksesBPRS(User $user)
    {
        return $user->status == 'BPRS';
    }

    public function aksesAdmin(User $user)
    {
        return $user->status == 'admin';
    }
}

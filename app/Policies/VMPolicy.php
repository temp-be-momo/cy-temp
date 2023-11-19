<?php

namespace App\Policies;

use App\User;
use App\VM;
use Illuminate\Auth\Access\HandlesAuthorization;

class VMPolicy
{
    use HandlesAuthorization;

    public function show(User $user, VM $vm)
    {
        return $user->isAdmin() || $user->id === $vm->user_id;
    }
}

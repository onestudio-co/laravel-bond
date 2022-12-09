<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserObserver
{
    public function creating(User $user)
    {
        if (Hash::needsRehash($user->password ?? '')) {
            $user->password = Hash::make($user->password ?? '');
        }
    }
}

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

    public function deleting(User $user)
    {
        $user->update([
            'name' => 'deleted user',
            'email' => 'deletedUser@email.com',
        ]);
    }
}

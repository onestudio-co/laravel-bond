<?php

namespace App\Listeners;

use App\Events\DeletedUser;

class DeleteUserRelations
{
    public function handle(DeletedUser $event)
    {
        $user = $event->user;

        $user->tokens()->delete();
        $user->notificationTokens()->delete();
        $user->notifications()->delete();
        $user->delete();
    }
}

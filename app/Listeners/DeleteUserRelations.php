<?php

namespace App\Listeners;

use App\Events\DeletedUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteUserRelations
{
    public function handle(DeletedUser $event)
    {
        $user = $event->user;

        $user->tokens()->delete();
        $user->notificationTokens()->delete();
        $user->notifications()->delete();
    }
}

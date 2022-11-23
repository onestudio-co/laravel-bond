<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        /*@var User $user*/
        $user = $request->user();

        $notifications = $user->notifications()->latest()->paginate();
        $unread_count = $user->unreadNotifications()->count();

        return NotificationResource::collection($notifications)
            ->additional([
                'meta' => [
                    'unread_count' => $unread_count,
                ],
            ]);
    }

    public function readAll(Request $request)
    {
        $request->user()->notifications->markAsRead();
        return response()->json(['message' => 'success']);
    }

    public function read(DatabaseNotification $notification)
    {
        $notification->markAsRead();
        return response()->json(['message' => 'success']);
    }
}
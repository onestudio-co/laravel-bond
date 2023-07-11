<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationTokenRequest;
use App\Models\NotificationToken;

class NotificationTokenController extends Controller
{
    public function __invoke(NotificationTokenRequest $request)
    {
        $user = $request->user();

        NotificationToken::query()->updateOrCreate(
            [
                'device_id' => $request->device_id,
                'device_type' => $request->device_type,
            ],
            [
                'user_id' => $user->id,
                'token' => $request->token,
            ],
        );

        return response()->json(['message' => 'success']);
    }
}

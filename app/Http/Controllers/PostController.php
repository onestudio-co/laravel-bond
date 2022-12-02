<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewPostNotification;
use Illuminate\Http\Request;
use MarkSitko\LaravelUnsplash\Facades\Unsplash;

class PostController extends Controller
{
    public function random(Request $request)
    {
        $post = Unsplash::randomPhoto()
            ->randomPhoto()
            ->toJson();
        /** @var User $user */
        $user = $request->user();
        $user->safeNotify(new NewPostNotification($post->id, $post->user->name,
            $post->user->profile_image->small));
    }
}

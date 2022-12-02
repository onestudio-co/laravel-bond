<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use App\Notifications\NewPostNotification;
use Illuminate\Http\Request;
use MarkSitko\LaravelUnsplash\Facades\Unsplash;

class PostController extends Controller
{

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $posts = Unsplash::randomPhoto()
            ->count(10)
            ->toJson();
        return response()->json(
            [
                'data' => $posts,
            ]
        );
    }

    public function show(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $post = Unsplash::photo($id)->toJson();
        } catch (ClientException $e) {
            abort(404, $e->getMessage());
        }
        return response()->json(
            [
                'data' => $post,
            ]
        );
    }

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

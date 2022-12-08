<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\User;
use App\Notifications\NewPostNotification;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use MarkSitko\LaravelUnsplash\Facades\Unsplash;

class PostController extends Controller
{
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $posts = Unsplash::randomPhoto()
            ->term($request->term)
            ->count(10)
            ->toJson();

        return PostResource::collection($posts);
    }

    public function show(Request $request, string $id): PostResource
    {
        try {
            $post = Unsplash::photo($id)->toJson();
        } catch (ClientException $e) {
            abort(404, $e->getMessage());
        }

        return new PostResource($post);
    }

    public function random(Request $request): PostResource
    {
        $post = Unsplash::randomPhoto()
            ->randomPhoto()
            ->toJson();
        /** @var User $user */
        $user = $request->user();
        $user->safeNotify(new NewPostNotification($post->id, $post->user->name,
            $post->user->profile_image->large));

        return new PostResource($post);
    }
}

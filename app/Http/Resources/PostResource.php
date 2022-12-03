<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid'        => $this->id,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'color'       => $this->color,
            'description' => $this->description,
            'urls'        => $this->urls,
            'likes'       => $this->likes,
            'author'      => [
                'uuid'               => $this->user->id,
                'updated_at'         => $this->user->updated_at,
                'username'           => $this->user->username,
                'name'               => $this->user->name,
                'first_name'         => $this->user->first_name,
                'last_name'          => $this->user->last_name,
                'avatar'             => $this->fullSizeImageUrl(),
                'twitter_username'   => $this->user->twitter_username,
                'instagram_username' => $this->user->instagram_username,
                'bio'                => $this->user->bio,
                'total_likes'        => $this->user->total_likes,
                'total_photos'       => $this->user->total_photos,
            ],
            'views'       => $this->views,
            'downloads'   => $this->downloads,
        ];
    }

    public function fullSizeImageUrl(): string
    {
        return str($this->user->profile_image->large)->replaceMatches('/&w=[0-9]*&h=[0-9]*/', '&w=512&h=512')->value();
    }
}


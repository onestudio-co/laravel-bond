<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'is_anonymous' => $this->is_anonymous,
        ];
    }

    public function withToken(string $createTokenKey)
    {
        $this->additional([
            'meta' => [
                'token' => $this->createToken($createTokenKey ?? 'user')->plainTextToken,
            ],
        ]);
        return $this;
    }
}

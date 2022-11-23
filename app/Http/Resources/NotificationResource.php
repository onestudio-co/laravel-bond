<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id'              => $this->id,
            'type'            => $this->type,
            'notifiable_type' => $this->notifiable_type,
            'notifiable_id'   => $this->notifiable_id,
            'code'            => $this?->data['code'],
            'title'           => $this->data['title'][App::getLocale()] ?? null,
            'body'            => $this->data['body'][App::getLocale()] ?? null,
            'sender_name'     => $this?->data['sender_name'],
            'sender_image'    => $this?->data['sender_image'],
            'read_at'         => $this->read_at,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
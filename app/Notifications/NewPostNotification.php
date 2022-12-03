<?php

namespace App\Notifications;

class NewPostNotification extends NotificationChannel
{
    public function __construct(
        public $id,
        public $sender_name,
        public $sender_image,
    ) {
    }

    public function getData(): array
    {
        return [
            'id' => $this->id,
        ];
    }

    public function getCode(): string
    {
        return 'new_post';
    }

    public function getAttributes(): array
    {
        return [
            'name' => $this->sender_name,
        ];
    }

    public function getSenderName(): ?string
    {
        return $this->sender_name;
    }

    public function getImage(): ?string
    {
        return str($this->sender_image)->replaceMatches('/&w=[0-9]*&h=[0-9]*/', '&w=512&h=512')->value();
    }
}

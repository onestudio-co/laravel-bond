<?php

namespace App\Notifications;

use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;
use NotificationChannels\Fcm\Resources\Notification as NotificationResource;

abstract class NotificationChannel extends Notification
{
    public $notifiable;

    public function via($notifiable)
    {
        $this->notifiable = $notifiable;
        $this->locale($notifiable->locale);

        return [
            DatabaseChannel::class,
            FcmChannel::class,
        ];
    }

    public function toFcm($notifiable)
    {
        $this->notifiable = $notifiable;
        $this->locale($notifiable->locale);

        return FcmMessage::create()
            ->setData($this->fcmData())
            ->setNotification($this->getNotification())
            ->setAndroid($this->getAndroidConfig($notifiable))
            ->setApns($this->getIosConfig($notifiable));
    }

    public function getIosConfig($notifiable): ApnsConfig
    {
        $analyticsLabel = 'ios-'.$this->getCode();
        $fcmOptions = ApnsFcmOptions::create()
            ->setAnalyticsLabel($analyticsLabel)
            ->setImage($this->getImage());

        return ApnsConfig::create()->setFcmOptions($fcmOptions);
    }

    public function getAndroidConfig($notifiable): AndroidConfig
    {
        $analyticsLabel = 'android-'.$this->getCode();

        $options = AndroidFcmOptions::create()
            ->setAnalyticsLabel($analyticsLabel);

        $notification = AndroidNotification::create()
            ->setChannelId('Taleb')
            ->setClickAction('FLUTTER_NOTIFICATION_CLICK')
            ->setImage($this->getImage());

        return AndroidConfig::create()
            ->setFcmOptions($options)
            ->setNotification($notification);
    }

    public function getNotification(): ?NotificationResource
    {
        return NotificationResource::create()
            ->setTitle($this->getTitle())
            ->setBody($this->getBody());
    }

    public function toDatabase($notifiable)
    {
        $this->notifiable = $notifiable;
        $this->locale($notifiable->locale);

        return [
            'title' => [
                'ar' => $this->getTitle('ar'),
                'en' => $this->getTitle('en'),
            ],
            'body' => [
                'ar' => $this->getBody('ar'),
                'en' => $this->getBody('en'),
            ],
            'code' => $this->getCode(),
            'data' => $this->getData(),
            'sender_name' => $this->getSenderName(),
            'sender_image' => $this->getImage(),
        ];
    }

    final public function fcmData(): array
    {
        $data = $this->getData();

        return [
            'data' => $data ? json_encode($data) : null,
            'code' => $this->getCode(),
        ];
    }

    abstract public function getCode(): string;

    public function getTitle($locale = null): string|null
    {
        return trans('notification.'.$this->getCode().'.title', $this->getAttributes(), $locale ?? $this->locale);
    }

    public function getBody($locale = null): string|null
    {
        return trans('notification.'.$this->getCode().'.body', $this->getAttributes(), $locale ?? $this->locale);
    }

    abstract public function getAttributes(): array;

    abstract public function getData(): array;

    public function getImage(): ?string
    {
        return asset('img/logo.png');
    }

    public function getSenderName(): ?string
    {
        return trans('Laravel Bond', [], $this->locale);
    }
}

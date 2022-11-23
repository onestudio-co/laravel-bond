<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_anonymous',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'is_anonymous' => false,
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_anonymous'      => 'boolean',
    ];

    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class);
    }

    public function routeNotificationForFcm()
    {
        return $this->notificationTokens()->pluck('token')->toArray();
    }

    public function safeNotify(Notification $notification)
    {
        rescue(fn() => $this->notify($notification));
    }
}
<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
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
        'is_anonymous' => 'boolean',
    ];

    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class);
    }

    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }

    public function routeNotificationForFcm()
    {
        return $this->notificationTokens()->pluck('token')->toArray();
    }

    public function safeNotify(Notification $notification)
    {
        try {
            $this->notify($notification);
        } catch (Exception $exception) {
            report($exception);
            Log::debug('fcm_not_send', [
                'exception' => $exception,
                'user' => $this,
                'notification' => $notification,
            ]);
        }
    }
}

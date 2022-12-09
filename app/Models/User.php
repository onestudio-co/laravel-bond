<?php

namespace App\Models;

use Based\Fluent\Fluent;
use Based\Fluent\Relations\Relation;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Fluent;

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

    private int $id;

    protected Carbon $email_verified_at;

    protected bool $is_anonymous = false;

    protected string $password;

    protected string $remember_token;

    protected string $email;

    protected string $name;

    protected Carbon $created_at;

    protected Carbon $updated_at;

    #[Relation]
    public Collection $notificationTokens;

    public Collection $socialLogins;

    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class);
    }

    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }

    public function routeNotificationForFcm(): array
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

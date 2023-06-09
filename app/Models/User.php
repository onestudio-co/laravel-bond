<?php

namespace App\Models;

use Based\Fluent\Fluent;
use Based\Fluent\Relations\Relation;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Fluent;
    use SoftDeletes;

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

    public int $id;

    public ?string $name = null;

    public ?string $email = null;

    public ?Carbon $email_verified_at = null;

    public string $password;

    public bool $is_anonymous = false;

    public string $remember_token;

    public Carbon $created_at;

    public ?Carbon $updated_at = null;

    public string $locale;

    #[Relation]
    public Collection $notificationTokens;

    public Collection $socialLogins;

    public function notificationTokens(): HasMany
    {
        return $this->hasMany(NotificationToken::class);
    }

    public function socialLogins(): HasMany
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

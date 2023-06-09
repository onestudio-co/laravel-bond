<?php

namespace App\Providers;

use App\Events\DeletedUser;
use App\Listeners\DeleteUserRelations;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        DeletedUser::class => [
            DeleteUserRelations::class,
        ],
    ];

    public function boot()
    {
        User::observe(UserObserver::class);
    }

    public function shouldDiscoverEvents()
    {
        return false;
    }
}

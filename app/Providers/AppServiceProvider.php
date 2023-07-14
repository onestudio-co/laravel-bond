<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Publiux\laravelcdn\Providers\AwsS3Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->alias(S3Provider::class, AwsS3Provider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        $this->app->alias(S3Provider::class, AwsS3Provider::class);
    }
}

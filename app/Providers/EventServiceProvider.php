<?php

namespace App\Providers;

use App\Listeners\CheckAccountLock;
use App\Listeners\LogFailedLogin;
use App\Listeners\LogPasswordChange;
use App\Listeners\LogSuccessfulLogin;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    protected $listen = [
        Login::class => [
            LogSuccessfulLogin::class,
            CheckAccountLock::class,
        ],
        Failed::class => [
            LogFailedLogin::class,
        ],
        PasswordReset::class => [
            LogPasswordChange::class,
        ]
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Listeners;

use App\Models\UserAction;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if($event->user instanceof \App\Models\User)
        {
            UserAction::create([
                'user_id' => $event->user->id,
                'action_type' => 'successful_login',
                'ip_address' => Request::ip(),
                'device_info' => Request::header('User-Agent')
            ]);
        }
    }
}

<?php

namespace App\Listeners;

use App\Models\UserAction;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Request;

class LogFailedLogin
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
    public function handle(Failed $event): void
    {

        if($event->user instanceof \App\Models\User){
            UserAction::create([
                'user_id' => $event->user->id,
                'action_type' => 'failed_login',
                'ip_address' => Request::ip(),
                'device_info' => Request::header('User-Agent')
            ]);
        }
    }
}

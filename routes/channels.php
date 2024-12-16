<?php

use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\Broadcast;


// Staff channel
Broadcast::channel('staff.notifications', function (Staff $user) {
    return $user->is_active;
});

Broadcast::channel('chat.{receiver_id}',function(Staff $staff, $receiver_id){
    return (int) $staff->id === (int) $receiver_id;
});

Broadcast::channel('user.notifications',function(User $user){
    return $user->is_active;
});
Broadcast::channel('user.certificate.approved.{id}',function(User $user,$id){
    return (int) $user->id == $id && $user->is_active;
});



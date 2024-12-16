<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAction extends Model
{
    protected $fillable = [
        'user_id',
        'action_type',
        'action_timestamp',
        'ip_address',
        'device_info'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

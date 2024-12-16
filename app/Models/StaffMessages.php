<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffMessages extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message'
    ];
    public function receiver():BelongsTo
    {
        return $this->belongsTo(Staff::class, 'sender_id');
    }
    public function sender():BelongsTo
    {
        return $this->belongsTo(Staff::class,'receiver_id');
    }

}

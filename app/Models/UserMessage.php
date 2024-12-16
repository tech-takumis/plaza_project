<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMessage extends Model
{
    /** @use HasFactory<\Database\Factories\UserMessageFactory> */
    use HasFactory;


    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message'
    ];
    public function receiver():BelongsTo
    {
        return $this->belongsTo(Staff::class,'receiver_id');
    }
    public function sender():BelongsTo
    {
        return $this->belongsTo(User::class,'sender_id');
    }
}

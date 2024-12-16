<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLogs extends Model
{
    protected $table = 'activity_logs';
    protected $fillable = [
        'action_performed',
        'user_type',
        'table_name',
        'column_name',
        'description'
    ];
}

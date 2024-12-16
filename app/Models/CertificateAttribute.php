<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateAttribute extends Model
{
    protected $fillable = [
        'certificate_id',
        'placeholder',
        'data_type',
        'is_required'
    ];

    public function certificate():BelongsTo
    {
       return  $this->belongsTo(Certificate::class,'certificate_id');
    }
}

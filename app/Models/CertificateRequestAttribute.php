<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateRequestAttribute extends Model
{
    // Specify the table name
    protected $table = 'certificate_request_attributes';

    protected $fillable = [
        'certificate_request_id',
        'attribute_name',
        'attribute_value',
    ];

    public function certificate_request(): BelongsTo
    {
        return $this->belongsTo(CertificateRequest::class, 'certificate_request_id');
    }
}

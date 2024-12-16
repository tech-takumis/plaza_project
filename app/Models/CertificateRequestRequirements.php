<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateRequestRequirements extends Model
{
    // Specify the table name
    protected $table = 'certificate_request_requirements';

    protected $fillable = [
        'certificate_request_id',
        'requirement_name',
        'requirement_value',
        'certificate_requirement_id',
    ];

    public function certificate_request(): BelongsTo
    {
        return $this->belongsTo(CertificateRequest::class, 'certificate_request_id');
    }

    public function certificate_requirement():BelongsTo
    {
        return $this->belongsTo(CertificateRequirements::class,'certificate_requirement_id');
    }
}

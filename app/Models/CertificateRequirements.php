<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateRequirements extends Model
{
    protected $fillable = [
        'certificate_id',
        'name',
        'description',
        'datatype',
        'is_required'
    ];

    public function certificate():BelongsTo
    {
        return $this->belongsTo(Certificate::class,'certificate_id');
    }

    public function certificate_request():HasMany
    {
        return $this->hasMany(CertificateRequestRequirements::class);
    }
}

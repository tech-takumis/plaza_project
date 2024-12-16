<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CertificateRequestRequirements;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateRequest extends Model
{
    protected $fillable = [
        'user_id',
        'certificate_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id');
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(CertificateRequestAttribute::class, 'certificate_request_id');
    }

    public function requirementValues(): HasMany
    {
        return $this->hasMany(CertificateRequestRequirements::class, 'certificate_request_id');
    }

}

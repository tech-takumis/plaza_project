<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    protected $table = 'certificates';
    protected $fillable = [
        'name',
        'description',
        'status',
        'validity',
        'template'
    ];

    public function certificateRequests(): HasMany
    {
        return $this->hasMany(CertificateRequest::class);
    }
    public function attributes()
    {
        return $this->hasMany(CertificateAttribute::class, 'certificate_id');
    }

    public function requirements()
    {
        return $this->hasMany(CertificateRequirements::class, 'certificate_id');
    }
}

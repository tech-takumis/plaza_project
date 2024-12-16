<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Residents extends Model
{
    /** @use HasFactory<\Database\Factories\ResidentsFactory> */
    use HasFactory;

    protected $table = 'residents';

    protected $fillable = [
        'address',
        'contact_number',
        'civil_status',
        'occupation',
        'Residency_Start_Date',
        'Remarks'
    ];

   public function users():HasMany
   {
    return $this->hasMany(User::class);
   }

   public function staff():HasMany
   {
    return $this->hasMany(Staff::class);
   }

}

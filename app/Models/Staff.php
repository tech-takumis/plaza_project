<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Enum\UserRole;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;


    protected $guard = 'staff';
    protected $connection = 'pgsql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     *
     */
    protected $fillable = [
        'name',
        'email',
        'position',
        'password',
    ];

    protected $cast = [
        'position' => UserRole::class,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'resident_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function is_staff(){
        return $this->is_admin != true;
    }

    public function messages():HasMany{
        return $this->hasMany(StaffMessages::class);
    }

    public function residents()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

}

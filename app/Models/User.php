<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $primaryKey = 'user_id';
    protected $keyType = 'int';
    public $incrementing = true;
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'phone',
        'location',
        'occupation',
        'institution_name',
        'company_name',
        'year',
        'help_areas',
        'is_volunteer',
        'blood_group',
        'role',
        'latitude',
        'longitude'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'help_areas' => 'array',
        'is_volunteer' => 'boolean',
         'latitude' => 'float',
        'longitude' => 'float'
    ];
    public function approvals()
    {
        return $this->hasMany(HelpPostApproval::class, 'helper_id', 'user_id');
    }
}

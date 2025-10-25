<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpPost extends Model
{
    use HasFactory;

    protected $primaryKey = 'post_id';
    protected $keyType = 'int';
    public $incrementing = true;
 public $timestamps = false;
    protected $fillable = [
        'user_id',
        'post_category',
        'post_title',
        'post_description',
        'post_location',
        'points',
        'post_creation_time',
        'blood_group',
        'hospital_name',
         'status',
         'latitude', 'longitude'
    ];

    public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'user_id');
}
public function pendingRequests()
{
    return $this->hasMany(HelpPostApproval::class, 'post_id')->where('status', 'pending');
}
public function approvals()
{
    return $this->hasMany(HelpPostApproval::class, 'post_id');
}

}
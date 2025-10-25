<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DonationApproval; 

class Donation extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    protected $casts = [
    'donation_images' => 'array',
];
protected $fillable = [
    'user_id', 'donation_title', 'donation_category', 'donation_description',
    'location', 'latitude', 'longitude', 'points', 'donation_images', 'status'
];
public function user() {
    return $this->belongsTo(User::class, 'user_id', 'user_id');
}
    public function approvals()
    {
        return $this->hasMany(DonationApproval::class, 'donation_id');
    }
public function acceptedRequest()
{
    return $this->approvals()->where('status', 'accepted')->first();
}
 public function pendingRequests()
{
    return $this->approvals()->where('status', 'pending');
}

public function acceptedRequests()
{
    return $this->approvals()->where('status', 'accepted');
}

public function completedRequests()
{
    return $this->approvals()->where('status', 'completed');
}

public function rejectedRequests()
{
    return $this->approvals()->where('status', 'rejected');
}

public function activeRequests()
{
    return $this->approvals()->whereIn('status', ['pending', 'accepted']);
}
    
}



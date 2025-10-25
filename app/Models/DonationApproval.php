<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationApproval extends Model
{
    protected $fillable = ['donation_id', 'requester_id', 'status', 'is_confirmed'];

    public function donation()
{
    return $this->belongsTo(Donation::class, 'donation_id');
}

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
}

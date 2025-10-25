<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpPostApproval extends Model
{
    protected $primaryKey = 'approval_id';

    protected $fillable = [
        'post_id',
        'helper_id',
        'status',
    ];

    public function post()
    {
        return $this->belongsTo(HelpPost::class, 'post_id');
    }

public function helper()
    {
        return $this->belongsTo(User::class, 'helper_id', 'user_id');
    }
  
}

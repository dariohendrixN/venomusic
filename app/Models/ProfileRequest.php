<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileRequest extends Model
{
    protected $fillable = [
        'sender_profile_id',
        'receiver_profile_id',
        'request_type',
        'status',
        'subject',
        'message',
        'requested_date',
        'answered_at',
    ];

    public function sender()
    {
        return $this->belongsTo(UserProfile::class, 'sender_profile_id');
    }

    public function receiver()
    {
        return $this->belongsTo(UserProfile::class, 'receiver_profile_id');
    }
}

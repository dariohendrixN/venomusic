<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Request;

class ProfileCollaboration extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'collaborator_profile_id',
        'initiator_profile_id',
        'collaboration_type',
        'procect_title',
        'notes',
        'started_at',
        'ended_at',
        'status',
        'approved_at',
    ];

    public function profile()
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }

    public function collaborator()
    {
        return $this->belongsTo(UserProfile::class, 'collaborator_profile_id');
    }

    public function initiator()
    {
        return $this->belongsTo(UserProfile::class, 'initiator_profile_id');
    }

}

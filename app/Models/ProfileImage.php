<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileImage extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_profile_id', 'path'];

    public function profile() {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }
}

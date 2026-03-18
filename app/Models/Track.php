<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_profile_id',
        'genre_id',
        'title',
        'audio_path',
    ];

    public function profile() {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }

    public function genre() {
        return $this->belongsTo(Genre::class);
    }
}

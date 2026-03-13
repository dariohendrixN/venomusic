<?php

namespace App\Models;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'added_by',
    ];

    public function parents() {
        return $this->belongsToMany(Genre::class, 'genre_parent', 'genre_id', 'parent_genre_id');
    }

    public function children() {
        return $this->belongsToMany(Genre::class, 'genre_parent', 'parent_genre_id', 'genre_id');
    }

    public function profiles() {
        return $this->belongsToMany(UserProfile::class, 'genre_profile', 'genre_id', 'user_profile_id');
    }
}

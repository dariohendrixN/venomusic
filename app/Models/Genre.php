<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
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
        return $this->belongsToMany(UserProfile::class, 'genre_profile');
    }
}

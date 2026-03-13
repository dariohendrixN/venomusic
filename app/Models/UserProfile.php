<?php

namespace App\Models;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'display_name',
        'address', 
        'city',
        'province',
        'region',
        'country',
        'phone',
        'profile_image'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function genres(){
        return $this->belongsToMany(Genre::class, 'genre_profile', 'user_profile_id', 'genre_id')
            ->withTimestamps();
    }
}

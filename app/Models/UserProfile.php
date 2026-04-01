<?php

namespace App\Models;

use App\Models\Genre;
use App\Models\ProfileCollaboration;
use App\Models\ProfileImage;
use App\Models\Track;
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
        'profile_image',
        'qobuz_url',
        'bandcamp_url',
        'deezer_url',
        'soundcloud_url',
        'amazon_music_url',
        'youtube_music_url',
        'apple_music_url',
        'spotify_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_profile', 'user_profile_id', 'genre_id')
            ->withTimestamps();
    }

    public function syncGenres(array $genreIds): void
    {
        $this->genres()->sync($genreIds);
    }

    public function images()
    {
        return $this->hasMany(ProfileImage::class, 'user_profile_id');
    }

    public function tracks()
    {
        return $this->hasMany(Track::class, 'user_profile_id');
    }

    public function collaborations()
    {
        return $this->hasMany(ProfileCollaboration::class, 'profile_id');
    }

    public function collaborationsWith()
    {
        return $this->hasMany(ProfileCollaboration::class, 'collaborator_profile_id');
    }
}

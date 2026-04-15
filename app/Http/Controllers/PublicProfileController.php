<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function show(UserProfile $profile): View {
        $profile->load([
            'user.roles',
            'images',
            'genres',
            'tracks.genre',
            'collaborations.collaborator'
        ]);
        return view('profiles.show', [
            'profile' => $profile]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DiscoverController extends Controller
{
    public function index() {
        $genres = Genre::orderBy('name')
            ->limit(50)
            ->get();
        return view('discover.index', compact('genres'));
    }

    public function search(Request $request) {
        $genreId = $request->get('genre_id');
        $role = $request->get('role');
        $profileSearch = trim((string)$request->get('profile_search'));
        $locationSearch = trim((string)$request->get('location_search'));
        $discoverableRoles = ['artist', 'producer', 'label', 'studio', 'venue'];
        $profiles= UserProfile::query()
            ->with(['user.roles', 'genres'])
            ->when($genreId, function ($q) use ($genreId) {
                $q->whereHas('genres', function ($sub) use ($genreId) {
                    $sub->where('genres.id', $genreId);
                });
            })
            ->when($role, function ($q) use ($role) {
                $q->whereHas('user.roles', function ($sub) use ($role) {
                    $sub->where('roles.name', $role)
                        ->whereIn('user_roles.status', ['auto_approved', 'manually_approved']);
                });
            })
            ->when($profileSearch, function ($q) use ($profileSearch) {
                $q->where(function ($sub) use ($profileSearch) {
                    $sub->where('name', 'like', "%{$profileSearch}%")
                        ->orWhere('surname', 'like', "%{$profileSearch}%")
                        ->orWhere('display_name', 'like', "%{$profileSearch}%");
                });
            })
            ->when($locationSearch, function ($q) use ($locationSearch) {
                $q->where(function ($sub) use ($locationSearch) {
                    $sub->where('city', 'like', "%{$locationSearch}%")
                        ->orWhere('province', 'like', "%{$locationSearch}%")
                        ->orWhere('region', 'like', "%{$locationSearch}%");
                });
            })
            ->whereHas('user.roles', function ($q) use ($discoverableRoles) {
                $q->whereIn('roles.name', $discoverableRoles)
                    ->whereIn('user_roles.status', ['auto_approved', 'manually_approved']);
            })
            ->get();
        return view('discover.index', [
            'profiles' => $profiles,
        ]);
    }
}

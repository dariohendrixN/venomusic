<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class ProfileGenreController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'genre_id' => ['required', 'exists:genres,id']
        ]);

        $profile = $request->user()->profile;
        $profile->genres()
                ->syncWithoutDetaching($request->genre_id);
        
        return back();
    }

    public function destroy(Request $request, Genre $genre) {
        $profile = $request->user()->profile;
        $profile->genres()
                ->detach($genre->id);
        
        return back();
    }
}

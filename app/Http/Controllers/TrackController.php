<?php

namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'genre_id' => ['nullable', 'exists:genres,id'],
            'audio_file' => ['required', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:10240'],
        ]);

        $profile = $request->user()->profile;

        $path = $request->file('audio_file')->store('tracks', 'public');

        $profile->tracks()->create([
            'title' => $request->title,
            'genre_id' => $request->genre_id ?: null,
            'path' => $path,
        ]);

        return back()->with('status', 'track-updated');
    }

    public function destroy(Request $request, Track $track) {
        $profile = $request->user()->profile;
        if ($track->user_profile_id !== $profile->id) {
            abort(403);
        }
        Storage::disk('public')->delete($track->path);
        $track->delete();
        
        return back()->with('status', 'track-deleted');
    }
}

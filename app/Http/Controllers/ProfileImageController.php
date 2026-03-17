<?php

namespace App\Http\Controllers;

use App\Models\ProfileImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileImageController extends Controller
{
    public function store(Request $request) {
        
        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        
        $profile = $request->user()->profile;
        
        foreach ($request->file('images', []) as $image) {
            $path = $image->store('profile-images', 'public');
            
            $profile->images()->create([
                'path' => $path
            ]);
        }
        
        return back()->with('status', 'gallery-updated');
    }

    public function destroy(Request $request, ProfileImage $image) {
        $profile = $request->user()->profile;
        if ($image->user_profile_id !== $profile->id) {
            abort(403);
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('status', 'gallery-updated');
    }
}



<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(
            'profile.images',
            'profile.genres',
            'profile.tracks.genre',
            'profile.collaborations.collaborator.user');

        
        return view('profile.edit', [
            'user' => $user,
            'profile' => $user->profile
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // generalità
            'name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string',  'lowercase', 'email', 'max:255'],
            'display_name' => ['required', 'string', 'max:255'],
            // profilo
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10500'], 
            // links sound
            'qobuz_url' => ['nullable', 'url'],
            'bandcamp_url' => ['nullable', 'url'],
            'deezer_url' => ['nullable', 'url'],
            'soundcloud_url' => ['nullable', 'url'],
            'amazon_music_url' => ['nullable', 'url'],
            'youtube_music_url' => ['nullable', 'url'],
            'apple_music_url' => ['nullable', 'url'],
            'spotify_url' => ['nullable', 'url'],
        ]);

        $user = $request->user();
        $profile = $user->profile;

        $user->update([
            'email' => $validated['email'],
        ]);

        $profileData = [
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'display_name' => $validated['display_name'],

            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'province' => $validated['province'] ?? null,
            'region' => $validated['region'] ?? null,
            'phone' => $validated['phone'] ?? null,

            'qobuz_url' => $validated['qobuz_url'] ?? null,
            'bandcamp_url' => $validated['bandcamp_url'] ?? null,
            'deezer_url' => $validated['deezer_url'] ?? null,
            'soundcloud_url' => $validated['soundcloud_url'] ?? null,
            'amazon_music_url'  => $validated['amazon_music_url'] ?? null,
            'youtube_music_url' => $validated['youtube_music_url'] ?? null,
            'apple_music_url' => $validated['apple_music_url'] ?? null,
            'spotify_url' => $validated['spotify_url'] ?? null,
            
        ];

        if ($request->hasFile('profile_image') && ! $request->user()->canUploadMedia()) {
            abort(403, 'Non sei autorizzato a caricare la foto profilo, richiedi un ruolo');
        }

        if ($request->hasFile('profile_image')) {
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }

            $profileData['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        $profile->update($profileData);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateLinks(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'qobuz_url' => ['nullable', 'url'],
        'bandcamp_url' => ['nullable', 'url'],
        'deezer_url' => ['nullable', 'url'],
        'soundcloud_url' => ['nullable', 'url'],
        'amazon_music_url' => ['nullable', 'url'],
        'youtube_music_url' => ['nullable', 'url'],
        'apple_music_url' => ['nullable', 'url'],
        'spotify_url' => ['nullable', 'url'],
    ]);

    $request->user()->profile->update([
        'qobuz_url' => $validated['qobuz_url'] ?? null,
        'bandcamp_url' => $validated['bandcamp_url'] ?? null,
        'deezer_url' => $validated['deezer_url'] ?? null,
        'soundcloud_url' => $validated['soundcloud_url'] ?? null,
        'amazon_music_url' => $validated['amazon_music_url'] ?? null,
        'youtube_music_url' => $validated['youtube_music_url'] ?? null,
        'apple_music_url' => $validated['apple_music_url'] ?? null,
        'spotify_url' => $validated['spotify_url'] ?? null,
    ]);

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password']
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

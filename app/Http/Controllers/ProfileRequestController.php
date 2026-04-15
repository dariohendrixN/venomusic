<?php

namespace App\Http\Controllers;

use App\Models\ProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileRequestController extends Controller
{
    public function store(Request $request): RedirectResponse {
        $validate = request()->validate([
            'receiver_profile_id' => ['required', 'exists:user_profiles,id'],
            'request_type' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'requested_date' => ['nullable', 'date'],
        ]);

        $senderProfile = $request->user()->profile;

        if (! $senderProfile) {
            abort(403, 'Profilo non disponibile');
        }
        if ((int) $senderProfile->id === (int) $validate['receiver_profile_id']) {
            return back()->withErrors([
                'receiver_profile_id' => 'Non puoi inviare una richiesta a te stesso'
            ]);
        }

        ProfileRequest::create([
            'sender_profile_id' => $senderProfile->id,
            'receiver_profile_id' => $validate['receiver_profile_id'],
            'request_type' => $validate['request_type'],
            'status' => 'pending',
            'subject' => $validate['subject'] ?? null,
            'message' => $validate['message'] ?? null,
            'requested_date' => $validate['requested_date'] ?? null,
            'answered_at' => null
        ]);

        return redirect()
            ->back()
            ->with('status', 'request-sent');
    }
    public function accept(ProfileRequest $profileRequest, Request $request): RedirectResponse {
        $profile = $request->user()->profile;

        if ($profileRequest->receiver_profile_id !== $profile->id) {
            abort(403);
        }

        $profileRequest->update([
            'status' => 'accepted',
            'answered_at' => now()
        ]);

        return back()->with('status', 'request-accepted');

    }

    public function reject(ProfileRequest $profileRequest, Request $request): RedirectResponse {
        $profile = $request->user()->profile;

        if ($profileRequest->receiver_profile_id !== $profile->id) {
            abort(403);
        }

        $profileRequest->update([
            'status' => 'rejected',
            'answered_at' => now()
        ]);

        return back()->with('status', 'request-rejected');
    }

    
}

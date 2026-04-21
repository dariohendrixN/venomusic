<?php

namespace App\Http\Controllers;

use App\Models\ProfileRequest;
use App\Models\UserProfile;
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

        if (! $request->user()->canSendRequests()){
            abort(403, 'Non sei autorizzato a inviare richieste');
        }

        $receiverProfile = UserProfile::with('user.roles')->findOrFail($validate['receiver_profile_id']);

        if ((int) $senderProfile->id === (int) $validate['receiver_profile_id']) {
            return back()->withErrors([
                'receiver_profile_id' => 'Non puoi inviare una richiesta a te stesso'
            ]);
        }

        $allowedTypes = $this->allowedRequestTypes($senderProfile, $receiverProfile);

        if (! in_array($validate['request_type'], $allowedTypes, true)) {
            return back()->withErrors([
                'request_type' => 'La categoria di professionisti a cui è iscritto questo profilo non permette di inviare questa tipo di richiesta',
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
        
        if ((int)$profileRequest->receiver_profile_id !== (int)$profile->id) {
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

        if ((int)$profileRequest->receiver_profile_id !== (int)$profile->id) {
            abort(403);
        }

        $profileRequest->update([
            'status' => 'rejected',
            'answered_at' => now()
        ]);

        return back()->with('status', 'request-rejected');
    }

    private function allowedRequestTypes(UserProfile $sender, UserProfile $receiver): array
{
    $senderRoles = $sender->user->activeRoles()->all();
    $receiverRoles = $receiver->user->activeRoles()->all();

    $senderIs = fn (string $role) => in_array($role, $senderRoles, true);
    $receiverIs = fn (string $role) => in_array($role, $receiverRoles, true);

    if ($senderIs('artist')) {
        $allowed = [];

        if ($receiverIs('studio')) {
            $allowed[] = 'booking';
        }

        if ($receiverIs('artist') || $receiverIs('producer')) {
            $allowed[] = 'collaboration';
        }

        if ($receiverIs('venue')) {
            $allowed[] = 'live-candidacy';
        }

        return $allowed;
    }

    if ($senderIs('producer')) {
        $allowed = [];

        if ($receiverIs('studio')) {
            $allowed[] = 'booking';
        }

        if ($receiverIs('artist') || $receiverIs('producer')) {
            $allowed[] = 'collaboration';
        }

        return $allowed;
    }

    if ($senderIs('label')) {
        $allowed = [];

        if ($receiverIs('artist') || $receiverIs('producer')) {
            $allowed[] = 'roster-proposal';
        }

        return $allowed;
    }

    return [];
}
}

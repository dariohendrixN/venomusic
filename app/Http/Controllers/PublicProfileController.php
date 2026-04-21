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

        $allowedRequestTypes = [];

        if (auth()->check() && auth()->user()->profile && auth()->user()->profile->id !== $profile->id) {
            
            $sender = auth()->user()->profile->load('user.roles');
            $receiver = $profile;
            $allowedRequestTypes = $this->allowedRequestTypes($sender, $receiver);
        }
        return view('profiles.show', [
            'profile' => $profile,
            'allowedRequestTypes' => $allowedRequestTypes
        ]);
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

<?php

namespace App\Http\Controllers;

use App\Models\ProfileCollaboration;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Js;

class ProfileCollaborationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'collaborator_profile_id' => ['required', 'exists:user_profiles,id'],
            'collaboration_type' => ['required', 'string', 'max:255'],
            'procect_title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date'],
        ]);

        $profile = $request->user()->profile;

        if ((int) $validated['collaborator_profile_id'] === (int) $profile->id) {
            return back()->withErrors([
                'collaborator_profile_id' => 'Non puoi collaborare con te stesso'
            ]);
        }


        $allowedRoles = ['artist', 'producer', 'label'];

        $collaborator = UserProfile::whereKey($validated['collaborator_profile_id'])
            ->whereHas('user.roles', function ($query) use ($allowedRoles) {
                $query->whereIn('roles.name', $allowedRoles)
                    ->whereIn('user_roles.status', ['auto_approved', 'manually_approved']);
            })
            ->first();

        if (! $collaborator) {
            return back()->withErrors([
                'collaborator_profile_id' => 'Il profilo scelto non configura tra i professionisti con cui puoi collaborare. Verifica che la professione tua o del collaboratore sia corretta, altrimenti verfica di aver scelto il tipo di collaborazione adeguata.',
            ]);
        }

        if (! $request->user()->canManageCollaborations()) {
            abort(403, 'Non sei autorizzato a gestire le collaborazioni');
        }

        $allowedTypes = $this->allowedCollaborationTypes($profile, $collaborator);

        if (! in_array($validated['collaboration_type'], $allowedTypes, true)) {
            return back()->withErrors([
                'collaboration_type' => 'Non puoi collaborare con questo professionista per questo tipo di collaborazione'
            ]);
        }

        $profile->collaborations()->create([
            'collaborator_profile_id' => $validated['collaborator_profile_id'],
            'initiator_profile_id' => $profile->id,
            'collaboration_type' => $validated['collaboration_type'],
            'project_title' => $validated['procect_title'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'started_at' => $validated['started_at'] ?? null,
            'ended_at' => $validated['ended_at'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('status', 'collaboration-added');
    }

    public function searchProfiles(Request $request):JsonResponse
    {
        if (! $request->user()->canManageCollaborations()) {
            abort(403, 'Non sei autorizzato a gestire le collaborazioni');
        }

        $profile = $request->user()->profile;
        $q = trim((string)$request->get('q', ''));
        $allowedRoles = ['artist', 'producer', 'label', 'admin'];

        $previousCollaboratorIds = $profile->collaborations()
            ->pluck('collaborator_profile_id')
            ->unique()
            ->values();

        $profiles = UserProfile::query()
            ->with('user.roles')
            ->where('id', '!=', $profile->id)
            ->whereHas('user.roles', function ($query) use ($allowedRoles) {
                $query->whereIn('roles.name', $allowedRoles)
                    ->whereIn('user_roles.status', ['auto_approved', 'manually_approved']);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('surname', 'like', "%{$q}%")
                        ->orWhere('display_name', 'like', "%{$q}%")
                        ->orWhere('city', 'like', "%{$q}%")
                        ->orWhere('province', 'like', "%{$q}%")
                        ->orWhere('region', 'like', "%{$q}%");
                });
            })
            ->get()
            ->sortByDesc(function ($candidate) use ($previousCollaboratorIds) {
                return $previousCollaboratorIds->contains($candidate->id);
            })
            ->take(8)
            ->values()
            ->map(function ($candidate) {
               
                return [
                    'id' => $candidate->id,
                    'full_name' => trim(($candidate->name ?? '') . ' ' . ($candidate->surname ?? '')),
                    'display_name' => $candidate->display_name,
                    'city' => $candidate->city,
                    'province' => $candidate->province,
                    'region' => $candidate->region,
                    'roles' => collect($candidate->user->visibleActiveRoleNames())->values()->all(),
                ];
            });

        return response()->json($profiles->all());
    }

    Public function accept(ProfileCollaboration $collaboration) {
        $profile =auth()->user()->profile;

        if($collaboration->collaborator_profile_id !== $profile->id) {
            abort(403);
        }
        
        $collaboration->update([
            'status' => 'accepted',
            'approved_at' => now()
        ]);

        return back()->with('status', 'collaboration-accepted');
    }

    Public function reject(ProfileCollaboration $collaboration) {
        $profile =auth()->user()->profile;

        if($collaboration->collaborator_profile_id !== $profile->id) {
            abort(403);
        }
        
        $collaboration->update([
            'status' => 'rejected',
        ]);

        return back()->with('status', 'collaboration-rejected');
    }

    private function allowedCollaborationTypes(UserProfile $profile, UserProfile $collaborator): array
{
    $myRoles = $profile->user->visibleActiveRoleNames()->pluck('name')->all();
    $otherRoles = $collaborator->user->visibleActiveRoleNames()->pluck('name')->all();

    $hasMe = fn (string $role) => in_array($role, $myRoles, true);
    $hasOther = fn (string $role) => in_array($role, $otherRoles, true);

    $allowed = [];

    if ($hasOther('label') || ($hasMe('label') && ($hasOther('artist') || $hasOther('producer')))) {
        $allowed[] = 'label-support';
    }

    if ($hasMe('producer') && $hasOther('producer')) {
        $allowed[] = 'production';
        $allowed[] = 'co-production';
        $allowed[] = 'remix';
    }

    if ($hasMe('artist') && $hasOther('artist')) {
        $allowed[] = 'featuring';
        $allowed[] = 'songwriting';
        $allowed[] = 'remix';
    }

    $artistProducerPair =
        ($hasMe('artist') && $hasOther('producer')) ||
        ($hasMe('producer') && $hasOther('artist'));

    if ($artistProducerPair) {
        $allowed[] = 'featuring';
        $allowed[] = 'production';
        $allowed[] = 'songwriting';
        $allowed[] = 'remix';
    }

    return array_values(array_unique($allowed));
}

    public function destroy(Request $request, ProfileCollaboration $collaboration)
    {
        $profile = $request->user()->profile;

        if ($collaboration->profile_id !== $profile->id) {
            abort(403);
        }

        if (! $request->user()->canManageCollaborations()) {
            abort(403, 'Non sei autorizzato a gestire le collaborazioni');
        }

        $collaboration->delete();

        return back()->with('status', 'collaboration-deleted');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ProfileCollaboration;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class ProfileCollaborationController extends Controller
{
    public function store(Request $request) {
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

        $profile->collaborations()->create($validated);

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
            
        return back()->with('status', 'collaboration-added');
    }


    public function searchProfiles(Request $request) {
        $profile = $request->user()->profile;
        $q = trim((string)$request->get('q', ''));
        $allowedRoles = ['artist', 'producer', 'label'];

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
                         ->orWhere('region', 'like', "%{$q}%")
                         ->orWhere('project_title', 'like', "%{$q}%");
                 });
            })  
            ->get()
            ->sortByDesc(function ($candidate) use ($previousCollaboratorIds) {
                return $previousCollaboratorIds->contains($candidate->id);
            })  
            ->take(8)
            ->values()
            ->map(function($candidate) {
                return [
                    'id' => $candidate->id,
                    'full_name' => trim(($candidate->name ?? '') .' ' . ($candidate->surname ?? '')),
                    'display_name' => $candidate->display_name,
                    'city' => $candidate->city,
                    'province' => $candidate->province,
                    'region' => $candidate->region,
                    'project_title' => $candidate->project_title,
                ];
            });  

        return response()->json($profiles); 
    }

    public function destroy(Request $request,ProfileCollaboration $collaboration) {
        $profile = $request->user()->profile;

        if ($collaboration->profile_id !== $profile->id) {
            abort(403);
        }

        $collaboration->delete();

        return back()->with('status', 'collaboration-deleted');
    }
}

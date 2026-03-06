<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleRequestController extends Controller
{
    public function requestRole(Request $request)
    {
        $request->validate(['role' => ['required', 'string', 'exists:roles,name']]);

        $user = $request->user();
        $requestedRole = $request->role;

        if (in_array($requestedRole, ['admin', 'observer'])) {
            return back()->with('error', 'Non puoi richiedere il ruolo di ' . $requestedRole);
        }

        if ($user->hasRole($requestedRole)) {
            return back()->with('error', 'Sei già un ' . $requestedRole);
        }

        if ($user->hasPendingRoleRequest($requestedRole)) {
            return back()->with('error', 'Hai già una richiesta per il ruolo di ' . $requestedRole);
        }

        $roleId = Role::where('name', $request->role)->value('id');

        $user->roles()->syncWithoutDetaching([
            $roleId => ['status' => 'pending', 'requested_at' => now()]
        ]);

        return back()->with('success', 'Richiesta inviata correttamente');
    }
}

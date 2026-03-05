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

        $roleId = Role::where('name', $request->role)->value('id');

        $user->roles()->syncWithoutDetaching([
            $roleId => ['status' => 'pending', 'requested_at' => now()]
        ]);

        return back()->with('success', 'Richiesta inviata correttamente');
    }
}

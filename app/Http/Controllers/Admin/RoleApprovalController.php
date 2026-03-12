<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RoleApprovalController extends Controller
{
    public function index() {
        
        $users = User::whereHas('roles', function ($query) {
            $query->where('user_roles.status', 'pending');
        })
        ->with(['roles', 'profile'])
        ->get();
        
        return view('admin.role-requests', compact('users'));
    }

    public function approve(User $user, string $role) {

        $roleModel = $user->roles()
        ->where('roles.name', $role)
        ->wherePivot('status', 'pending')
        ->first();

        if (!$roleModel) {
            return back()->with('error', 'Richiesta non trovata');
        }

        $user->roles()->updateExistingPivot($roleModel->id, [
            'status' => 'manually_approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null
        ]);
        return back()->with('success', 'Richiesta ruolo approvata correttamente');
    }

    public function reject(User $user, string $role) {

        $roleModel = $user->roles()
            ->where('roles.name', $role)
            ->wherePivot('status', 'pending')
            ->first();

        if (!$roleModel) {
            return back()->with('error', 'Richiesta non trovata');
        }

        $user->roles()->updateExistingPivot($roleModel->id, [
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by' => auth()->id(),
            'rejection_reason' => 'Richiesta ruolo rifiutata dagli admin'
        ]);
            return back()->with('success', 'Richiesta ruolo rifiutata correttamente');
        
    }
}

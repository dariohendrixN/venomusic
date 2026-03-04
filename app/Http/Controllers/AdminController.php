<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        if(auth()->user()->isAdmin()){
            return view('admin.dashboard');
        }
        abort(403, 'Non sei autorizzato');
    }
}

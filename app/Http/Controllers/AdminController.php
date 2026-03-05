<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // if(! auth()->user()->isAdmin()){
        //     abort(403, 'Non sei autorizzato a vedere questa pagina');
        // }
        // return view('admin.dashboard');
    }
}
// ** funzione spostata su RoleMiddleware **

<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function search(Request $request) {
        $q = trim((string)$request->get('q'));

        $genres = Genre::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%$q%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($genres);
    }
}

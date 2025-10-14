<?php

namespace App\Http\Controllers\Artists;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->input('q');

        if ($term) {
            $clients = User::query()
                ->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->get(['id', 'first_name', 'last_name', 'email']);
        } else {
            $clients = User::query()
                ->get(['id', 'first_name', 'last_name', 'email']);
        }

        return response()->json($clients);
    }
}

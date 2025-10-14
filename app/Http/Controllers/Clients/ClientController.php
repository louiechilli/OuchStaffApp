<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::orderBy('created_at', 'desc')->paginate(15);

        Log::info('ClientController@index', [
            'count' => $clients->count(),
            'user_id' => optional(auth()->user())->id,
        ]);

        return view('pages.clients.index', compact('clients'));
    }
    
    public function show(Client $client)
    {
        Log::info('ClientController@show', [
            'client_id' => $client->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        return view('pages.clients.show', compact('client'));
    }

    public function search(Request $request)
    {
        $term = $request->input('q');

        $clients = Client::query()
            ->where('first_name', 'like', "%{$term}%")
            ->orWhere('last_name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'phone']);

        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name'  => 'nullable|string|max:191',
            'email'      => 'nullable|email|max:191|unique:clients,email',
            'phone'      => 'nullable|string|max:30',
        ]);

        $client = Client::create($validated);

        return response()->json($client, 201);
    }
}

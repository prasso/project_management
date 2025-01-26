<?php

namespace Prasso\ProjectManagement\Http\Controllers;

use Prasso\ProjectManagement\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('projects')->latest()->paginate(10);
        
        if (request()->wantsJson()) {
            return response()->json($clients);
        }

        return view('prasso-pm::clients.index', compact('clients'));
    }

    public function create()
    {
        return view('prasso-pm::clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Client created successfully', 'client' => $client]);
        }

        return redirect()->route('prasso-pm.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        return view('prasso-pm::clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('prasso-pm::clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('prasso-pm.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Client deleted successfully']);
        }

        return redirect()->route('prasso-pm.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}

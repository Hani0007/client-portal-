<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\clients as Client;

class AgencyClientController extends Controller
{
    /**
     * Display a listing of clients
     */
    public function index()
    {
        $agency = auth()->user()?->agency;
        if (!$agency) {
            return redirect()->route('agency.create');
        }
        $clients = Client::where('agency_id', $agency->id)->orderBy('name')->get();
        return view('agency.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client
     */
    public function create()
    {
        $agency = auth()->user()?->agency;
        if (!$agency) {
            return redirect()->route('agency.create');
        }
        return view('agency.clients.create');
    }

    /**
     * Store a newly created client
     */
    public function store(StoreClientRequest $request)
    {
        $agency = auth()->user()->agency;
        Client::create([
            'agency_id' => $agency->id,
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email ?? null,
            'company_name' => $request->company_name ?? null,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client added.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\clients as Client;
use App\Models\deliverables as Deliverable;
use App\Models\Invoice;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display the client dashboard
     */
    public function index()
    {
        $client = auth()->user()?->client;
        if (!$client) {
            return view('client', ['clientExists' => false]);
        }

        $recentDeliverables = Deliverable::whereHas('project', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->orderByDesc('created_at')->take(5)->get();

        $recentInvoices = Invoice::whereHas('project', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->orderByDesc('created_at')->take(5)->get();

        return view('client', [
            'clientExists' => true,
            'client' => $client,
            'recentDeliverables' => $recentDeliverables,
            'recentInvoices' => $recentInvoices,
        ]);
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

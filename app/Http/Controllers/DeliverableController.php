<?php

namespace App\Http\Controllers;

use App\Models\clients as Client;
use App\Models\deliverables as Deliverable;
use Illuminate\Http\Request;

class DeliverableController extends Controller
{
    /**
     * Approve a deliverable
     */
    public function approve(Deliverable $deliverable)
    {
        $client = auth()->user()?->client;
        if (!$client) {
            abort(403);
        }

        // Verify deliverable belongs to client's project
        if ($deliverable->project->client_id !== $client->id) {
            abort(403);
        }

        // Create or update approval
        \App\Models\approvales::updateOrCreate(
            [
                'deliverable_id' => $deliverable->id,
                'client_id' => $client->id,
            ],
            [
                'status' => 'approved',
                'comments' => null,
            ]
        );

        return redirect()->route('client.home')->with('success', 'Deliverable approved.');
    }

    /**
     * Reject a deliverable (request changes)
     */
    public function reject(Deliverable $deliverable, Request $request)
    {
        $client = auth()->user()?->client;
        if (!$client) {
            abort(403);
        }

        // Verify deliverable belongs to client's project
        if ($deliverable->project->client_id !== $client->id) {
            abort(403);
        }

        // Validate comments
        $request->validate([
            'comments' => ['required', 'string'],
        ]);

        // Create or update approval with comments
        \App\Models\approvales::updateOrCreate(
            [
                'deliverable_id' => $deliverable->id,
                'client_id' => $client->id,
            ],
            [
                'status' => 'rejected',
                'comments' => $request->comments,
            ]
        );

        return redirect()->route('client.home')->with('success', 'Change request submitted.');
    }
}

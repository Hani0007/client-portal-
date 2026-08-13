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

        // Check if all deliverables in the project are approved
        $project = $deliverable->project;
        $allDeliverables = $project->deliverables;
        $allApproved = true;

        foreach ($allDeliverables as $d) {
            $approval = $d->approvals()->where('client_id', $client->id)->first();
            if (!$approval || $approval->status !== 'approved') {
                $allApproved = false;
                break;
            }
        }

        // If all deliverables are approved and project has deliverables, update project status
        if ($allApproved && $allDeliverables->isNotEmpty()) {
            $project->update(['status' => 'completed']);
        }

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
        $approval = \App\Models\approvales::updateOrCreate(
            [
                'deliverable_id' => $deliverable->id,
                'client_id' => $client->id,
            ],
            [
                'status' => 'rejected',
                'comments' => $request->comments,
            ]
        );

        // Parse comments into individual change request items (split by newlines)
        $changeItems = array_filter(array_map('trim', explode("\n", $request->comments)));

        // Delete existing change request items for this approval
        \App\Models\ChangeRequestItem::where('approval_id', $approval->id)->delete();

        // Create new change request items
        foreach ($changeItems as $item) {
            if (!empty($item)) {
                \App\Models\ChangeRequestItem::create([
                    'approval_id' => $approval->id,
                    'description' => $item,
                    'is_completed' => false,
                ]);
            }
        }

        return redirect()->route('client.home')->with('success', 'Change request submitted.');
    }
}

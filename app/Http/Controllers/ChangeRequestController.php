<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequestItem;
use App\Models\approvales;
use Illuminate\Http\Request;

class ChangeRequestController extends Controller
{
    /**
     * Get change request items for an approval
     */
    public function show(approvales $approval)
    {
        // Validate that user is agency owner
        if (!auth()->user()?->hasRole('agency_owner')) {
            abort(403);
        }

        $items = ChangeRequestItem::where('approval_id', $approval->id)->get();

        return response()->json([
            'items' => $items,
        ]);
    }

    /**
     * Update change request items completion status
     */
    public function update(Request $request, approvales $approval)
    {
        // Validate that user is agency owner
        if (!auth()->user()?->hasRole('agency_owner')) {
            abort(403);
        }

        // Validate the request
        $request->validate([
            'completed_items' => ['array'],
            'completed_items.*' => ['integer'],
        ]);

        // Get all change request items for this approval
        $items = ChangeRequestItem::where('approval_id', $approval->id)->get();

        // Update completion status for each item
        foreach ($items as $item) {
            $isCompleted = in_array($item->id, $request->completed_items ?? []);
            $item->update(['is_completed' => $isCompleted]);
        }

        // Check if all items are completed
        $allCompleted = $items->every(fn($item) => $item->is_completed);

        // If all completed, update approval status to 'completed'
        if ($allCompleted && $items->isNotEmpty()) {
            $approval->update(['status' => 'completed']);
        }

        return redirect()->back()->with('success', 'Change request items updated.');
    }
}

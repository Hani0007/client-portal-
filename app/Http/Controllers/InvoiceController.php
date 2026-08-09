<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $user = auth()->user();

        // Agency owners can view invoices from their agency
        if ($user->hasRole('agency_owner')) {
            $agency = $user->agency;
            if (!$agency || $invoice->project->agency_id !== $agency->id) {
                abort(403);
            }
        }
        // Clients can view invoices from projects assigned to them
        elseif ($user->can('pay-invoice')) {
            // Allow clients to view invoices to pay them
        } else {
            abort(403);
        }

        return view('invoice_show', compact('invoice'));
    }
}

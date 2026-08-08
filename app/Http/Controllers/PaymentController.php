<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    /**
     * Create a Stripe checkout session for the invoice
     */
    public function checkout(Invoice $invoice, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Authorize: agency owners can act for their agency; clients can pay invoices assigned to them
        if ($user->hasRole('agency_owner')) {
            $agency = $user->agency;
            if (!$agency || $invoice->project->agency_id !== $agency->id) {
                abort(403);
            }
        } elseif ($user->can('pay-invoice')) {
            // Client with pay-invoice permission - allow payment
            // No strict client record check required
        } else {
            abort(403);
        }

        if (!config('services.stripe.secret')) {
            return response()->json(['error' => 'Stripe not configured'], 500);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $amountCents = (int) round($invoice->amount * 100);

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Invoice #' . $invoice->id,
                            'description' => $invoice->project?->name,
                        ],
                        'unit_amount' => $amountCents,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('invoices.success', $invoice->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('invoices.show', $invoice->id),
                'metadata' => [
                    'invoice_id' => $invoice->id,
                ],
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle successful payment redirect
     */
    public function success(Invoice $invoice, Request $request)
    {
        $sessionId = $request->query('session_id');

        // Update invoice status to paid if not already paid
        if ($invoice->status !== 'paid' && $sessionId) {
            $invoice->update(['status' => 'paid']);

            // Create payment record
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount_paid' => $invoice->amount,
                'payment_method' => 'card',
                'paid_at' => now(),
            ]);
        }

        return view('invoice_success', compact('invoice'));
    }
}

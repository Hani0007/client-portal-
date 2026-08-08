<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Invoice;
use App\Models\Payment;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $invoiceId = $session->metadata->invoice_id ?? null;

            if ($invoiceId) {
                $invoice = Invoice::find($invoiceId);

                if ($invoice && $invoice->status !== 'paid') {
                    $invoice->update(['status' => 'paid']);

                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount_paid' => $invoice->amount,
                        'payment_method' => 'card',
                        'paid_at' => now(),
                    ]);
                }
            }
        }

        return response('Webhook handled', 200);
    }
}

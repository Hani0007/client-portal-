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
        $signature = $request->header('Stripe-Signature');

        \Log::info('Webhook received', [
            'event_type' => 'webhook_received',
            'signature' => $signature ? 'present' : 'missing',
            'payload_length' => strlen($payload)
        ]);

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            \Log::error('Webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'error' => 'Invalid webhook signature'
            ], 400);
        }

        \Log::info('Webhook event processed', [
            'event_type' => $event->type
        ]);

        // Handle Stripe event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $invoiceId = $session->metadata->invoice_id ?? null;

                \Log::info('Checkout session completed', [
                    'invoice_id' => $invoiceId,
                    'metadata' => $session->metadata
                ]);

                if ($invoiceId) {
                    $invoice = Invoice::find($invoiceId);

                    if ($invoice) {
                        \Log::info('Invoice found', [
                            'invoice_id' => $invoice->id,
                            'current_status' => $invoice->status
                        ]);

                        if ($invoice->status !== 'paid') {
                            $invoice->update(['status' => 'paid']);

                            Payment::create([
                                'invoice_id' => $invoice->id,
                                'amount_paid' => $invoice->amount,
                                'payment_method' => 'card',
                                'paid_at' => now(),
                            ]);

                            \Log::info('Invoice marked as paid', [
                                'invoice_id' => $invoice->id
                            ]);
                        } else {
                            \Log::info('Invoice already paid', [
                                'invoice_id' => $invoice->id
                            ]);
                        }
                    } else {
                        \Log::error('Invoice not found', [
                            'invoice_id' => $invoiceId
                        ]);
                    }
                } else {
                    \Log::error('No invoice_id in session metadata');
                }
                break;

            case 'payment_intent.succeeded':
                \Log::info('Payment intent succeeded');
                break;

            case 'payment_intent.payment_failed':
                \Log::info('Payment intent failed');
                break;
        }

        return response()->json([
            'success' => true
        ]);
    }
}


<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Invoice;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PayInvoice extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function pay()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Invoice #' . $this->invoice->id,
                        'description' => $this->invoice->project?->name,
                    ],
                    'unit_amount' => (int) ($this->invoice->amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('invoices.success', $this->invoice->id) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('projects.show', $this->invoice->project_id),
            'metadata' => [
                'invoice_id' => $this->invoice->id,
            ],
        ]);

        return redirect($session->url);
    }

    public function render()
    {
        return view('livewire.pay-invoice');
    }
}

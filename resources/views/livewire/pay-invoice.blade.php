<div class="rounded-2xl border border-slate-200 bg-white p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500">Invoice #{{ $invoice->id }}</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($invoice->amount, 2) }}</p>
        </div>

        @if($invoice->status === 'paid')
            <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">Paid</span>
        @else
            <button wire:click="pay" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-50">
                <span wire:loading.remove>Pay Now</span>
                <span wire:loading>Redirecting...</span>
            </button>
        @endif
    </div>
</div>

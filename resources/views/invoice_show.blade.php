@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Invoice #{{ $invoice->id }}</h1>

    <div class="mb-6">
        <p class="text-sm text-slate-500">Project: {{ $invoice->project?->name ?? 'N/A' }}</p>
        <p class="text-lg font-semibold">Amount: ${{ number_format($invoice->amount, 2) }}</p>
        <p class="text-sm text-slate-500">Status: {{ ucfirst($invoice->status) }}</p>
        <p class="text-sm text-slate-500">Due: {{ $invoice->due_date?->toFormattedDateString() ?? '-' }}</p>
    </div>

    <div class="mb-6">
        @if($invoice->status !== 'paid')
            <livewire:pay-invoice :invoice="$invoice" />
        @else
            <div class="rounded-xl bg-emerald-50 p-4 text-emerald-700">This invoice is paid.</div>
        @endif
    </div>
</div>
@endsection

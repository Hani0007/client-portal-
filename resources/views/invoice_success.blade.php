@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto text-center py-12">
    <h1 class="text-2xl font-bold mb-4">Thank you — payment received</h1>
    <p class="text-slate-600 mb-6">We received payment for Invoice #{{ $invoice->id }}. This page is for UX only — the webhook confirmed the payment.</p>

    <a href="{{ route('client.home') }}" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-white">Return to dashboard</a>
</div>
@endsection

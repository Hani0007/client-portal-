@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-3xl rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-slate-900">Client Portal</h1>
        <p class="mt-2 text-sm text-slate-600">Only users with the <strong>client</strong> role can access this page.</p>

        <div class="mt-8 rounded-3xl border border-slate-200 bg-slate-50 p-6">
            <p class="text-sm text-slate-700">Use this area to review deliverables, request changes, and pay invoices.</p>
        </div>
    </div>
@endsection

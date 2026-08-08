@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-3xl rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
                <p class="text-sm text-slate-500">Welcome back, {{ auth()->user()->name }}.</p>
            </div>
            <div class="rounded-full bg-slate-100 px-4 py-2 text-sm text-slate-700">Role: {{ auth()->user()->getRoleNames()->join(', ') ?: 'No role assigned' }}</div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            @role('agency_owner')
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="mb-2 text-lg font-semibold text-slate-900">Agency Portal</h2>
                    <p class="text-sm text-slate-600">You can create projects, upload deliverables, and manage client work.</p>
                    <a href="{{ route('agency.home') }}" class="mt-4 inline-flex rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Open Agency Area</a>
                </div>
            @endrole

            @role('client')
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="mb-2 text-lg font-semibold text-slate-900">Client Portal</h2>
                    <p class="text-sm text-slate-600">You can review deliverables, send messages, and pay invoices.</p>
                    <a href="{{ route('client.home') }}" class="mt-4 inline-flex rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Open Client Area</a>
                </div>
            @endrole

            @unlessrole('agency_owner|client')
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="mb-2 text-lg font-semibold text-slate-900">No role assigned</h2>
                    <p class="text-sm text-slate-600">Please ask your administrator to assign a role to your account.</p>
                </div>
            @endunlessrole
        </div>
    </div>
@endsection

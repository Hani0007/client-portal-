@extends('layouts.app')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.14em] text-emerald-600">Clients</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">Client directory</h1>
            <p class="mt-1 text-sm text-slate-600">View and manage clients for your agency.</p>
            @unless($hasAgency)
                <div class="mt-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    Your account does not yet have an agency record. Add an agency during registration or contact your administrator.
                </div>
            @endunless
        </div>

        @if($hasAgency)
            <a href="{{ route('clients.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-600/20 transition hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Client
            </a>
        @else
            <a href="{{ route('agency.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-600/20 transition hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create Agency
            </a>
        @endif
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Clients</h2>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($clients as $client)
                <div class="flex flex-col gap-2 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $client->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $client->company_name ?? $client->email }}</p>
                    </div>
                    <div class="space-x-2 text-xs font-medium">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">Client ID #{{ $client->id }}</span>
                    </div>
                </div>
            @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-slate-500">No clients registered yet.</p>
                        @if($hasAgency)
                            <a href="{{ route('clients.create') }}" class="mt-2 inline-block text-sm font-medium text-emerald-600 hover:text-emerald-700">Add your first client →</a>
                        @else
                            <a href="{{ route('agency.create') }}" class="mt-2 inline-block text-sm font-medium text-emerald-600 hover:text-emerald-700">Create your agency first →</a>
                        @endif
                    </div>
                @endforelse
        </div>
    </div>
@endsection

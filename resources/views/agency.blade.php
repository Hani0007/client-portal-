@extends('layouts.app')

@section('content')
    {{-- PAGE HEADER --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.14em] text-emerald-600">Agency Dashboard</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-sm text-slate-600">Here's what's happening across your projects today.</p>
        </div>

        @if (! ($agencyExists ?? false))
            <a href="{{ route('agency.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-600/20 transition hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Agency
            </a>
        @else
            <a href="{{ route('projects.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-600/20 transition hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Project
            </a>
        @endif
    </div>

    @unless($agencyExists ?? false)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6 text-center">
            <h2 class="text-lg font-semibold text-rose-800">Agency record not found</h2>
            <p class="mt-2 text-sm text-rose-700">You need to create an agency before you can manage clients, projects, or invoices.</p>
            <div class="mt-4 flex justify-center">
                <a href="{{ route('agency.create') }}" class="inline-flex items-center justify-center rounded-xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-rose-600/20 transition hover:bg-rose-700">Create agency now</a>
            </div>
        </div>
    @else
        {{-- STATS ROW --}}
        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Active Projects</p>
                <p class="mt-2 text-2xl font-bold text-slate-900">{{ $activeProjectsCount ?? 0 }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Clients</p>
                <p class="mt-2 text-2xl font-bold text-slate-900">{{ $clientsCount ?? 0 }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pending Approvals</p>
                <p class="mt-2 text-2xl font-bold text-amber-600">{{ $pendingApprovalsCount ?? 0 }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Unpaid Invoices</p>
                <p class="mt-2 text-2xl font-bold text-rose-600">${{ number_format($unpaidInvoicesTotal ?? 0, 2) }}</p>
            </div>

        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- PROJECTS LIST --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white">
                    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Recent Projects</h2>
                        <a href="{{ route('projects.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">View all</a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($projects ?? [] as $project)
                            <a href="{{ route('projects.show', $project->id) }}" class="flex items-center justify-between px-6 py-4 transition hover:bg-slate-50">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $project->name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">Client: {{ $project->client->name ?? 'Unassigned' }}</p>
                                </div>

                                @php
                                    $statusStyles = [
                                        'in_progress' => 'bg-blue-50 text-blue-700',
                                        'waiting_approval' => 'bg-amber-50 text-amber-700',
                                        'approved' => 'bg-emerald-50 text-emerald-700',
                                        'completed' => 'bg-slate-100 text-slate-700',
                                    ];
                                    $statusLabels = [
                                        'in_progress' => 'In Progress',
                                        'waiting_approval' => 'Waiting Approval',
                                        'approved' => 'Approved',
                                        'completed' => 'Completed',
                                    ];
                                @endphp
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusStyles[$project->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $statusLabels[$project->status] ?? ucfirst($project->status) }}
                                </span>
                            </a>
                        @empty
                            <div class="px-6 py-10 text-center">
                                <p class="text-sm text-slate-500">No projects yet.</p>
                                <a href="{{ route('projects.create') }}" class="mt-2 inline-block text-sm font-medium text-emerald-600 hover:text-emerald-700">Create your first project →</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- SIDEBAR: QUICK ACTIONS + INVOICES + CLIENTS --}}
            <div class="space-y-6">

                {{-- Quick Actions --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-base font-semibold text-slate-900">Quick Actions</h2>
                    <div class="mt-4 space-y-2">
                        <a href="{{ route('clients.create') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                            </svg>
                            Add New Client
                        </a>
                        <a href="{{ route('projects.create') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Create Project
                        </a>
                        <a href="{{ route('invoices.create') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Send Invoice
                        </a>
                    </div>
                </div>

                {{-- Recent Invoices --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-slate-900">Recent Invoices</h2>
                        <a href="{{ route('invoices.create') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Create new</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($invoices ?? [] as $invoice)
                            <div class="flex items-center justify-between p-3 border border-slate-100 rounded-xl">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Invoice #{{ $invoice->id }}</p>
                                    <p class="text-xs text-slate-500">{{ $invoice->project->client->name ?? 'Unknown' }} • ${{ number_format($invoice->amount, 2) }}</p>
                                </div>
                                @php
                                    $statusStyles = [
                                        'sent' => 'bg-amber-50 text-amber-700',
                                        'paid' => 'bg-emerald-50 text-emerald-700',
                                        'pending' => 'bg-amber-50 text-amber-700',
                                    ];
                                @endphp
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusStyles[$invoice->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No invoices yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Clients --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-slate-900">Clients</h2>
                        <a href="{{ route('clients.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View all</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($clients ?? [] as $client)
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-sm font-semibold text-emerald-700">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ $client->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $client->company_name ?? $client->email }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No clients added yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    @endunless
@endsection
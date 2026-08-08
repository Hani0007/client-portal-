@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-emerald-600">Project details</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $project->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $project->client->name ?? 'Client not assigned' }} &bull; Status: {{ ucfirst(str_replace('_', ' ', $project->status)) }}</p>
                </div>

                <a href="{{ route('projects.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to projects</a>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-4 lg:col-span-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <h2 class="text-sm font-semibold text-slate-900">Description</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $project->description }}</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Created</p>
                            <p class="mt-2 text-sm text-slate-900">{{ $project->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Client</p>
                            <p class="mt-2 text-sm text-slate-900">{{ $project->client->name ?? 'Unassigned' }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <h2 class="text-sm font-semibold text-slate-900">Deliverables</h2>
                        <p class="mt-2 text-sm text-slate-600">{{ $project->deliverables->count() }} submitted deliverable{{ $project->deliverables->count() === 1 ? '' : 's' }}.</p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <h2 class="text-sm font-semibold text-slate-900">Invoices</h2>
                        <p class="mt-2 text-sm text-slate-600">{{ $project->invoices->count() }} invoice{{ $project->invoices->count() === 1 ? '' : 's' }} attached.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-base font-semibold text-slate-900">Invoices</h2>
            <div class="mt-4 divide-y divide-slate-100">
                @forelse($project->invoices as $invoice)
                    <div class="flex items-center justify-between px-4 py-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">${{ number_format($invoice->amount, 2) }}</p>
                            <p class="mt-1 text-xs text-slate-500">Due {{ $invoice->due_date->format('M d, Y') }} • {{ ucfirst($invoice->status) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No invoices have been created for this project yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

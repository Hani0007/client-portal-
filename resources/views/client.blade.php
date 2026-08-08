@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-4xl rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Client Dashboard</h1>
                <p class="text-sm text-slate-500">Manage deliverables, review projects, and handle invoices from one place.</p>
            </div>
            <div class="text-sm text-slate-500">@if(isset($client)) Account: <strong>{{ $client->name }}</strong> @endif</div>
        </div>

        {{-- DEBUG: show current auth info to help diagnose missing client relation --}}
        @if(config('app.debug'))
            <div class="mb-4 rounded-md border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700">
                <div>Debug: auth id = {{ auth()->id() ?? 'guest' }}</div>
                <div>Debug: roles = {{ implode(', ', auth()->user()?->getRoleNames()->toArray() ?? []) }}</div>
                <div>Debug: user->client id = {{ auth()->user()?->client?->id ?? 'null' }}</div>
            </div>
        @endif

        @if (! ($clientExists ?? true))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6 text-center">
                <h2 class="text-lg font-semibold text-rose-800">Client record not found</h2>
                <p class="mt-2 text-sm text-rose-700">Your account is not connected to a client record. Please contact your agency administrator.</p>
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 mb-6">
                        <h2 class="text-lg font-semibold">Recent Deliverables</h2>
                        @if(($recentDeliverables ?? collect())->isEmpty())
                            <p class="text-sm text-slate-500 mt-3">No recent deliverables.</p>
                        @else
                            <ul class="mt-3 space-y-3">
                                @foreach($recentDeliverables as $d)
                                    <li class="flex items-center justify-between p-3 border rounded-md">
                                        <div>
                                            <p class="font-medium">{{ $d->title }}</p>
                                            <p class="text-xs text-slate-500">Project: {{ $d->project->name ?? '—' }} • {{ $d->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @if($d->file_path)
                                                <a href="{{ asset('storage/' . $d->file_path) }}" class="text-sm text-emerald-600">Download</a>
                                            @endif
                                            <a href="#" class="text-sm text-slate-500">Request changes</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="text-lg font-semibold">Projects</h2>
                        @php $projects = $client->projects()->orderByDesc('created_at')->take(5)->get(); @endphp
                        @if($projects->isEmpty())
                            <p class="text-sm text-slate-500 mt-3">No projects assigned yet.</p>
                        @else
                            <ul class="mt-3 space-y-3">
                                @foreach($projects as $p)
                                    <li class="flex items-center justify-between p-3 border rounded-md">
                                        <div>
                                            <p class="font-medium">{{ $p->name }}</p>
                                            <p class="text-xs text-slate-500">Status: {{ ucfirst(str_replace('_', ' ', $p->status)) }}</p>
                                        </div>
                                        <a href="{{ route('projects.show', $p->id) }}" class="text-sm text-emerald-600">Open</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 mb-6">
                        <h3 class="text-base font-semibold">Invoices</h3>
                        @if(($recentInvoices ?? collect())->isEmpty())
                            <p class="text-sm text-slate-500 mt-3">No invoices yet.</p>
                        @else
                            <ul class="mt-3 space-y-3">
                                @foreach($recentInvoices as $inv)
                                    <li class="flex items-center justify-between p-3 border rounded-md">
                                        <div>
                                            <p class="font-medium">Invoice #{{ $inv->id }}</p>
                                            <p class="text-xs text-slate-500">Amount: ${{ number_format($inv->amount, 2) }} • Due {{ $inv->due_date?->toFormattedDateString() }}</p>
                                        </div>
                                                <div>
                                                    @if($inv->status !== 'paid')
                                                        <button data-invoice-id="{{ $inv->id }}" class="pay-invoice-btn text-sm inline-flex items-center rounded-md bg-emerald-600 px-3 py-1 text-white">Pay</button>
                                                    @else
                                                        <span class="text-sm text-slate-500">Paid</span>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h3 class="text-base font-semibold">Contact Agency</h3>
                        <p class="text-sm text-slate-500 mt-2">Send a quick message or request support from your agency.</p>
                        <a href="#" class="mt-4 inline-block rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Message Agency</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

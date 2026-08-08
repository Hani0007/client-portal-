@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Send Invoice</h1>
                <p class="text-sm text-slate-500">Create and send an invoice to a client for completed work.</p>
            </div>
            <a href="{{ route('agency.home') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm">Return to dashboard</a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-rose-50 border border-rose-200 p-4 text-rose-700">
                <ul class="text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('invoices.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700">Project
                    <select name="project_id" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm">
                        <option value="">Select a project</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} @if($p->client) - {{ $p->client->name }} @endif</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Amount (USD)
                        <input name="amount" type="number" step="0.01" min="0" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm" />
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Due Date
                        <input name="due_date" type="date" class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm" />
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Notes (optional)
                    <textarea name="notes" rows="4" class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm"></textarea>
                </label>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('agency.home') }}" class="mr-3 inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Send Invoice</button>
            </div>
        </form>
    </div>
@endsection

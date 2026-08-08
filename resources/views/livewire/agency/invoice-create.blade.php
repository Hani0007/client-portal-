@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-emerald-600">Send Invoice</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">Create and send an invoice</h1>
                    <p class="mt-1 text-sm text-slate-600">Select a project and enter the billing details.</p>
                </div>
                <a href="{{ route('clients.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to clients</a>
            </div>

            <form wire:submit.prevent="createInvoice" class="space-y-5">
                <div>
                    <label for="project_id" class="block text-sm font-medium text-slate-700">Project</label>
                    <select id="project_id" wire:model="project_id" class="mt-2 block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Select a project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }} &bull; {{ $project->client->name ?? 'No client' }}</option>
                        @endforeach
                    </select>
                    @error('project_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-slate-700">Amount</label>
                        <input id="amount" type="number" step="0.01" wire:model="amount" class="mt-2 block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:ring-emerald-500" />
                        @error('amount') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-slate-700">Due Date</label>
                        <input id="due_date" type="date" wire:model="due_date" class="mt-2 block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:ring-emerald-500" />
                        @error('due_date') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Send Invoice</button>
                </div>
            </form>
        </div>
    </div>
@endsection

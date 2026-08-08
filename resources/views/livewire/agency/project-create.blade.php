@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-emerald-600">New Project</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">Create a new project</h1>
                    <p class="mt-1 text-sm text-slate-600">Add project details and assign it to an existing client.</p>
                </div>
                <a href="{{ route('projects.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to projects</a>
            </div>

            <form wire:submit.prevent="createProject" class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Project name</label>
                    <input id="name" type="text" wire:model="name" class="mt-2 block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                    <textarea id="description" wire:model="description" rows="4" class="mt-2 block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    @error('description') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-slate-700">Client</label>
                        <select id="client_id" wire:model="client_id" class="mt-2 block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select a client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} @if($client->company_name) &bull; {{ $client->company_name }} @endif</option>
                            @endforeach
                        </select>
                        @error('client_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                        <select id="status" wire:model="status" class="mt-2 block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="in_progress">In Progress</option>
                            <option value="waiting_approval">Waiting Approval</option>
                            <option value="approved">Approved</option>
                            <option value="completed">Completed</option>
                        </select>
                        @error('status') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Create Project</button>
                </div>
            </form>
        </div>
    </div>
@endsection

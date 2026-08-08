@extends('layouts.app')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.14em] text-emerald-600">Projects</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">Projects for {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-sm text-slate-600">Manage projects for your agency, review statuses, and create new work.</p>
        </div>

        @if ($hasAgency)
            <a href="{{ route('projects.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-600/20 transition hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Project
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

    <div class="space-y-6">
        @if (! $hasAgency)
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6 text-center">
                <h2 class="text-lg font-semibold text-rose-800">No agency record found</h2>
                <p class="mt-2 text-sm text-rose-700">Create an agency first so you can manage projects for your organization.</p>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('agency.create') }}" class="inline-flex items-center justify-center rounded-xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-700">Create agency now</a>
                </div>
            </div>
        @else
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Recent Projects</h2>
                    <p class="mt-1 text-sm text-slate-500">Projects created across your agency.</p>
                </div>
                <a href="{{ route('projects.create') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Create project</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($projects as $project)
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
                        <p class="text-sm text-slate-500">No projects found yet.</p>
                        <a href="{{ route('projects.create') }}" class="mt-2 inline-block text-sm font-medium text-emerald-600 hover:text-emerald-700">Create your first project →</a>
                    </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
@endsection

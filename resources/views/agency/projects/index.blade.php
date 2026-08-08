@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Projects</h1>
                <p class="text-sm text-slate-500">Manage projects for your agency.</p>
            </div>
            <a href="{{ route('projects.create') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-white">New Project</a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">{{ session('success') }}</div>
        @endif

        @if($projects->isEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center">
                <p class="text-sm text-slate-500">No projects yet. Create your first project.</p>
                <a href="{{ route('projects.create') }}" class="mt-4 inline-block rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Create project</a>
            </div>
        @else
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <ul class="space-y-3">
                    @foreach($projects as $project)
                        <li class="flex items-center justify-between p-3 border-b last:border-b-0">
                            <div>
                                <p class="font-medium">{{ $project->name }}</p>
                                <p class="text-xs text-slate-500">Client: {{ $project->client->name ?? 'Unassigned' }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('projects.show', $project->id) }}" class="text-sm text-emerald-600">Open</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Create Project</h1>
            <p class="text-sm text-slate-500">Start a new project for a client.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-rose-50 border border-rose-200 p-4 text-rose-700">
                <ul class="text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('projects.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Project Name
                    <input name="name" value="{{ old('name') }}" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm" />
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Client (optional)
                    <select name="client_id" class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm">
                        <option value="">Unassigned</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Description
                    <textarea name="description" class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm">{{ old('description') }}</textarea>
                </label>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('projects.index') }}" class="mr-3 inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm">Back</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Create Project</button>
            </div>
        </form>
    </div>
@endsection

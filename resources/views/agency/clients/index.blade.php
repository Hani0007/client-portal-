@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Clients</h1>
                <p class="text-sm text-slate-500">Manage clients for your agency.</p>
            </div>
            <a href="{{ route('clients.create') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-white">Add Client</a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">{{ session('success') }}</div>
        @endif

        @if($clients->isEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center">
                <p class="text-sm text-slate-500">No clients yet. Add your first client.</p>
                <a href="{{ route('clients.create') }}" class="mt-4 inline-block rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Add client</a>
            </div>
        @else
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <ul class="space-y-3">
                    @foreach($clients as $client)
                        <li class="flex items-center justify-between p-3 border-b last:border-b-0">
                            <div>
                                <p class="font-medium">{{ $client->name }}</p>
                                <p class="text-xs text-slate-500">{{ $client->company_name ?? $client->email }}</p>
                            </div>
                            <div class="text-sm text-slate-500">Added {{ $client->created_at->diffForHumans() }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Add Client</h1>
            <p class="text-sm text-slate-500">Add a new client to your agency. The email and password you set will be used for client login.</p>
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

        <form method="POST" action="{{ route('clients.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Name
                    <input name="name" value="{{ old('name') }}" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm" />
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Email (for client login)
                    <input name="email" type="email" value="{{ old('email') }}" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm" />
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Password (for client login)
                    <input name="password" type="password" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm" />
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Confirm Password
                    <input name="password_confirmation" type="password" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm" />
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Company Name
                    <input name="company_name" value="{{ old('company_name') }}" class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm" />
                </label>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('clients.index') }}" class="mr-3 inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm">Back</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Add Client</button>
            </div>
        </form>
    </div>
@endsection

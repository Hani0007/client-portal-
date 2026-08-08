@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-emerald-600">Create Agency</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">Add your agency details</h1>
                    <p class="mt-1 text-sm text-slate-600">Create an agency record so you can manage clients, projects, and invoices.</p>
                </div>
                <a href="{{ route('agency.home') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Return to dashboard</a>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Show general Livewire errors and a small debug line for authenticated user id --}}
            @if ($errors->has('general'))
                <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                    {{ $errors->first('general') }}
                </div>
            @endif

            <div class="mb-4 text-xs text-slate-500">Debug: auth id = {{ auth()->id() ?? 'guest' }}</div>

            <form wire:submit.prevent="createAgency" class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Agency Name</label>
                    <input id="name" type="text" wire:model="name" class="mt-2 block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="brand_color" class="block text-sm font-medium text-slate-700">Brand Color</label>
                    <input id="brand_color" type="color" wire:model="brand_color" class="mt-2 h-12 w-20 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('brand_color') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Create Agency</button>
                </div>
            </form>
        </div>
    </div>
@endsection

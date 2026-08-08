@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ $project->name }}</h1>
                <p class="text-sm text-slate-500">Client: {{ $project->client->name ?? 'Unassigned' }}</p>
            </div>
            <div class="text-sm text-slate-500">Status: {{ ucwords(str_replace('_', ' ', $project->status)) }}</div>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-4 mb-6">
            <h2 class="text-lg font-semibold mb-3">Deliverables</h2>

            @if ($deliverables->isEmpty())
                <p class="text-sm text-slate-500">No deliverables yet.</p>
            @else
                <ul class="space-y-3">
                    @foreach($deliverables as $d)
                        <li class="flex items-center justify-between p-3 border-b last:border-b-0">
                            <div>
                                <p class="font-medium">{{ $d->title }}</p>
                                <p class="text-xs text-slate-500">Uploaded {{ $d->created_at->diffForHumans() }}</p>
                            </div>
                            @if($d->file_path)
                                <a href="{{ asset('storage/' . $d->file_path) }}" class="text-sm text-emerald-600">Download</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 mb-6">
        <h2 class="text-lg font-semibold mb-3">Invoices</h2>
        @php $invoices = $project->invoices()->orderByDesc('created_at')->get(); @endphp
        @if($invoices->isEmpty())
            <p class="text-sm text-slate-500">No invoices for this project yet.</p>
        @else
            <ul class="space-y-3">
                @foreach($invoices as $inv)
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

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <h3 class="text-base font-semibold mb-2">Add Deliverable</h3>
        <form method="POST" action="{{ route('projects.deliverables.store', $project->id) }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Title
                    <input name="title" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm" />
                </label>
            </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">File (optional)
                        <input type="file" name="file" class="mt-2 block w-full text-sm" />
                    </label>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('projects.index') }}" class="mr-3 inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm">Back</a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Upload</button>
                </div>
            </form>
        </div>
    </div>
@endsection

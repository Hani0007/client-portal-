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
                        @php
                            $approval = $d->approvals()->first();
                            $approvalStatus = $approval ? $approval->status : null;
                            $approvalComments = $approval ? $approval->comments : null;
                        @endphp
                        <li class="flex items-center justify-between p-3 border-b last:border-b-0">
                            <div class="flex-1">
                                <p class="font-medium">{{ $d->title }}</p>
                                <p class="text-xs text-slate-500">Uploaded {{ $d->created_at->diffForHumans() }}</p>
                                @if($approvalStatus === 'approved')
                                    <span class="inline-block mt-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Approved</span>
                                @elseif($approvalStatus === 'rejected' || $approvalStatus === 'completed')
                                    @php
                                        $changeRequestItems = $approval ? $approval->changeRequestItems : collect();
                                        $pendingItems = $changeRequestItems->where('is_completed', false);
                                        $allCompleted = $changeRequestItems->isNotEmpty() && $changeRequestItems->every(fn($item) => $item->is_completed);
                                    @endphp
                                    <div class="mt-2 p-2 bg-amber-50 border border-amber-200 rounded-lg">
                                        <p class="text-xs font-medium text-amber-700">
                                            @if($allCompleted)
                                                All Changes Completed
                                            @else
                                                Changes Requested ({{ $pendingItems->count() }} pending)
                                            @endif
                                        </p>
                                        @if($changeRequestItems->isNotEmpty())
                                            <button onclick="openChangeRequestModal({{ $approval->id }})" class="text-xs text-amber-600 mt-1 underline">View Details</button>
                                        @endif
                                    </div>
                                @elseif($approvalStatus === 'pending')
                                    <span class="inline-block mt-1 text-xs font-medium text-slate-600 bg-slate-100 px-2 py-1 rounded-full">Pending Approval</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                @if($d->file_path)
                                    <a href="{{ asset('storage/' . $d->file_path) }}" class="text-sm text-emerald-600">Download</a>
                                @endif
                            </div>
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

    <!-- Change Request Modal -->
    <div id="changeRequestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Change Request Details</h3>
                <button onclick="closeChangeRequestModal()" class="text-slate-500 hover:text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="changeRequestForm" method="POST" action="">
                @csrf
                <div id="changeRequestItems" class="space-y-3 mb-4">
                    <!-- Items will be loaded dynamically -->
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeChangeRequestModal()" class="px-4 py-2 text-sm rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openChangeRequestModal(approvalId) {
            const modal = document.getElementById('changeRequestModal');
            const form = document.getElementById('changeRequestForm');
            const itemsContainer = document.getElementById('changeRequestItems');

            // Set form action
            form.action = '/approvals/' + approvalId + '/change-requests';

            // Fetch change request items
            fetch('/api/change-requests/' + approvalId)
                .then(response => response.json())
                .then(data => {
                    itemsContainer.innerHTML = '';
                    data.items.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'flex items-start gap-3 p-3 bg-slate-50 rounded-lg';
                        div.innerHTML = `
                            <input type="checkbox" name="completed_items[]" value="${item.id}" ${item.is_completed ? 'checked' : ''} class="mt-1 w-4 h-4 text-emerald-600 rounded border-slate-300">
                            <label class="text-sm text-slate-700 flex-1">${item.description}</label>
                        `;
                        itemsContainer.appendChild(div);
                    });
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                })
                .catch(error => console.error('Error:', error));
        }

        function closeChangeRequestModal() {
            const modal = document.getElementById('changeRequestModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection

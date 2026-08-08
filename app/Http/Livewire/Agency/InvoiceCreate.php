<?php

namespace App\Http\Livewire\Agency;

use App\Models\projects as Project;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InvoiceCreate extends Component
{
    public ?int $project_id = null;
    public string $amount = '';
    public string $due_date = '';
    public $projects = [];

    protected function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
        ];
    }

    public function mount()
    {
        $agency = Auth::user()?->agency;

        if (! $agency) {
            return redirect()->route('agency.create');
        }

        $this->projects = Project::with('client')
            ->where('agency_id', $agency->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function createInvoice()
    {
        $validated = $this->validate();

        Invoice::create([
            'project_id' => $validated['project_id'],
            'amount' => $validated['amount'],
            'status' => 'sent',
            'due_date' => $validated['due_date'],
        ]);

        session()->flash('success', 'Invoice created successfully.');

        return redirect()->route('clients.index');
    }

    public function render()
    {
        return view('livewire.agency.invoice-create');
    }
}

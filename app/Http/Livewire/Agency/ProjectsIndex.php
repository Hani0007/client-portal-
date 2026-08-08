<?php

namespace App\Http\Livewire\Agency;

use App\Models\approvales;
use App\Models\clients as Client;
use App\Models\Invoice;
use App\Models\projects as Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectsIndex extends Component
{
    public $projects = [];
    public int $activeProjectsCount = 0;
    public int $clientsCount = 0;
    public int $pendingApprovalsCount = 0;
    public float $unpaidInvoicesTotal = 0.0;
    public bool $hasAgency = false;

    public function mount()
    {
        $agency = Auth::user()?->agency;
        $this->hasAgency = $agency !== null;

        if (! $this->hasAgency) {
            $this->projects = collect();
            return;
        }

        $this->projects = Project::with('client')
            ->where('agency_id', $agency->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $this->activeProjectsCount = Project::where('agency_id', $agency->id)->count();
        $this->clientsCount = Client::where('agency_id', $agency->id)->count();
        $this->pendingApprovalsCount = approvales::whereHas('deliverable', function ($query) use ($agency) {
            $query->whereHas('project', fn ($project) => $project->where('agency_id', $agency->id));
        })->where('status', 'pending')->count();
        $this->unpaidInvoicesTotal = Invoice::whereHas('project', fn ($query) => $query->where('agency_id', $agency->id))
            ->where('status', 'pending')
            ->sum('amount');
    }

    public function render()
    {
        return view('livewire.agency.projects-index');
    }
}

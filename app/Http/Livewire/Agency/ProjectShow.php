<?php

namespace App\Http\Livewire\Agency;

use App\Models\projects as Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectShow extends Component
{
    public Project $project;

    public function mount($project)
    {
        $agency = Auth::user()?->agency;

        if (! $agency) {
            return redirect()->route('agency.create');
        }

        $this->project = Project::with(['client', 'deliverables', 'invoices'])
            ->where('agency_id', $agency->id)
            ->findOrFail($project);
    }

    public function render()
    {
        return view('livewire.agency.project-show');
    }
}

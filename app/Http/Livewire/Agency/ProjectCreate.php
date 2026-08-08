<?php

namespace App\Http\Livewire\Agency;

use App\Models\clients as Client;
use App\Models\projects as Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectCreate extends Component
{
    public string $name = '';
    public string $description = '';
    public string $status = 'in_progress';
    public ?int $client_id = null;
    public $clients = [];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', 'string', 'in:in_progress,waiting_approval,approved,completed'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
        ];
    }

    public function mount()
    {
        $agency = Auth::user()?->agency;

        if (! $agency) {
            return redirect()->route('agency.create');
        }

        $this->clients = Client::where('agency_id', $agency->id)->orderBy('name')->get();
    }

    public function createProject()
    {
        $validated = $this->validate();

        $agency = Auth::user()->agency;

        Project::create([
            'agency_id' => $agency->id,
            'client_id' => $validated['client_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        session()->flash('success', 'Project created successfully.');

        return redirect()->route('projects.index');
    }

    public function render()
    {
        return view('livewire.agency.project-create');
    }
}

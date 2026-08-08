<?php

namespace App\Http\Livewire\Agency;

use App\Models\clients as Client;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClientsIndex extends Component
{
    public $clients = [];
    public bool $hasAgency = false;

    public function mount()
    {
        $agency = Auth::user()?->agency;
        $this->hasAgency = $agency !== null;

        if (! $this->hasAgency) {
            $this->clients = collect();
            return;
        }

        $this->clients = Client::where('agency_id', $agency->id)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.agency.clients-index');
    }
}

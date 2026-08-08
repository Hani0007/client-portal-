<?php

namespace App\Http\Livewire\Agency;

use App\Models\clients as Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ClientsCreate extends Component
{
    public string $name = '';
    public string $email = '';
    public string $company_name = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function mount()
    {
        if (! Auth::user()?->agency) {
            return redirect()->route('agency.create');
        }
    }

    public function createClient()
    {
        Log::debug('ClientsCreate: attempt', [
            'user_id' => Auth::id(),
            'name' => $this->name,
            'email' => $this->email,
            'company_name' => $this->company_name,
        ]);

        $validated = $this->validate();

        $agency = Auth::user()->agency;
       
        if (!$agency) {
            Log::error('ClientsCreate: no agency found for user', ['user_id' => Auth::id()]);
            $this->addError('general', 'No agency found for your account.');
            return;
        }

        Log::debug('ClientsCreate: creating client', [
            'agency_id' => $agency->id,
            'user_id' => Auth::id(),
            'data' => $validated,
        ]);

        try {
            $client = Client::create([
                'agency_id' => $agency->id,
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'company_name' => $validated['company_name'],
            ]);

     

            Log::debug('ClientsCreate: created successfully', ['client_id' => $client->id]);

            session()->flash('success', 'Client created successfully.');

            return redirect()->route('clients.index');
        } catch (\Throwable $e) {
            Log::error('ClientsCreate: failed to create client', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addError('general', 'Failed to create client: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.agency.clients-create');
    }
}

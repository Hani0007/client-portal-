<?php

namespace App\Http\Livewire\Agency;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AgencyCreate extends Component
{
    public string $name = '';
    public string $brand_color = '#10B981';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'brand_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function mount()
    {
        if (Auth::user()?->agency) {
            return redirect()->route('agency.home');
        }
    }

    public function createAgency()
    {
        $this->validate();

        Log::debug('AgencyCreate: attempt', ['user_id' => Auth::id(), 'name' => $this->name, 'brand_color' => $this->brand_color]);

        try {
            $agency = Auth::user()->agency()->create([
                'name' => $this->name,
                'brand_color' => $this->brand_color,
                'logo_path' => null,
            ]);

            Log::debug('AgencyCreate: created', ['agency_id' => $agency->id ?? null]);

            session()->flash('success', 'Agency created successfully.');

            return redirect()->route('agency.home');
        } catch (\Throwable $e) {
            Log::error('AgencyCreate: failed to create agency', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->addError('general', 'Failed to create agency. See logs for details.');
        }
    }

    public function render()
    {
        return view('livewire.agency.agency-create');
    }
}

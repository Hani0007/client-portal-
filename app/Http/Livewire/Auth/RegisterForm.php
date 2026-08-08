<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class RegisterForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = '';
    public string $agency_name = '';
    public array $roles = [];

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'string', 'in:' . implode(',', $this->roles)],
        ];

        if ($this->role === 'agency_owner') {
            $rules['agency_name'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    public function mount()
    {
        $this->roles = Role::orderBy('name')->pluck('name')->toArray();

        if (empty($this->roles)) {
            $this->roles = ['agency_owner', 'client'];
        }
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $user->assignRole($this->role);

        if ($this->role === 'agency_owner') {
            $user->agency()->create([
                'name' => $this->agency_name,
                'brand_color' => '#10B981',
                'logo_path' => null,
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register-form');
    }
}

<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Livewire\Agency\AgencyCreate;
use App\Http\Livewire\Agency\ClientsCreate;
use App\Http\Livewire\Agency\ClientsIndex;
use App\Http\Livewire\Agency\InvoiceCreate;
use App\Http\Livewire\Agency\ProjectCreate;
use App\Http\Livewire\Agency\ProjectShow;
use App\Http\Livewire\Agency\ProjectsIndex;
use App\Models\approvales;
use App\Models\clients as Client;
use App\Models\Invoice;
use App\Models\User;
use App\Models\projects as Project;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::middleware('role:agency_owner')->group(function () {
        Route::get('/agency', function () {
            $agency = auth()->user()?->agency;

            $projectQuery = Project::with('client');
            $clientQuery = Client::query();
            $activeProjectsCount = 0;
            $clientsCount = 0;
            $pendingApprovalsCount = 0;
            $unpaidInvoicesTotal = 0;

            if ($agency) {
                $projectQuery->where('agency_id', $agency->id);
                $clientQuery->where('agency_id', $agency->id);
                $activeProjectsCount = Project::where('agency_id', $agency->id)->count();
                $clientsCount = Client::where('agency_id', $agency->id)->count();
                $pendingApprovalsCount = approvales::whereHas('deliverable', function ($query) use ($agency) {
                    $query->whereHas('project', fn ($project) => $project->where('agency_id', $agency->id));
                })->where('status', 'pending')->count();
                $unpaidInvoicesTotal = Invoice::whereHas('project', fn ($query) => $query->where('agency_id', $agency->id))
                    ->where('status', 'pending')
                    ->sum('amount');
            }

            return view('agency', [
                'agencyExists' => $agency !== null,
                'projects' => $agency ? $projectQuery->orderByDesc('created_at')->take(5)->get() : collect(),
                'clients' => $agency ? $clientQuery->orderBy('name')->take(5)->get() : collect(),
                'activeProjectsCount' => $activeProjectsCount,
                'clientsCount' => $clientsCount,
                'pendingApprovalsCount' => $pendingApprovalsCount,
                'unpaidInvoicesTotal' => $unpaidInvoicesTotal,
            ]);
        })->name('agency.home');

        Route::get('/projects', ProjectsIndex::class)->name('projects.index');
        Route::get('/projects/create', ProjectCreate::class)->name('projects.create');
        Route::get('/projects/{project}', ProjectShow::class)->name('projects.show');

        Route::get('/clients', ClientsIndex::class)->name('clients.index');
        Route::get('/clients/create', ClientsCreate::class)->name('clients.create');
        Route::get('/agency/create', AgencyCreate::class)->name('agency.create');

        Route::get('/invoices/create', InvoiceCreate::class)->name('invoices.create');
    });

    Route::middleware('role:client')->group(function () {
        Route::view('/client', 'client')->name('client.home');
    });
});

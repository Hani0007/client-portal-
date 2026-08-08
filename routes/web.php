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
use App\Models\deliverables as Deliverable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Checkout endpoint (creates a Stripe Checkout Session) - accessible to agency owners and clients
    Route::post('/invoices/{invoice}/checkout', function (App\Models\Invoice $invoice) {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Authorize: agency owners can act for their agency; clients can pay invoices assigned to them
        if ($user->hasRole('agency_owner')) {
            $agency = $user->agency;
            if (! $agency || $invoice->project->agency_id !== $agency->id) {
                abort(403);
            }
        } elseif ($user->can('pay-invoice')) {
            // Client with pay-invoice permission - allow payment
            // No strict client record check required
        } else {
            abort(403);
        }

        if (! config('services.stripe.secret')) {
            return response()->json(['error' => 'Stripe not configured'], 500);
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $amountCents = (int) round($invoice->amount * 100);

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Invoice #' . $invoice->id,
                            'description' => $invoice->project?->name,
                        ],
                        'unit_amount' => $amountCents,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('invoices.success', $invoice->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('invoices.show', $invoice->id),
                'metadata' => [
                    'invoice_id' => $invoice->id,
                ],
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    })->name('invoices.checkout');

    Route::middleware('role:agency_owner')->group(function () {
        Route::get('/agency', function () {
            $agency = auth()->user()?->agency;

            $projectQuery = Project::with('client');
            $clientQuery = Client::query();
            $invoiceQuery = Invoice::with('project.client');
            $activeProjectsCount = 0;
            $clientsCount = 0;
            $pendingApprovalsCount = 0;
            $unpaidInvoicesTotal = 0;

            if ($agency) {
                $projectQuery->where('agency_id', $agency->id);
                $clientQuery->where('agency_id', $agency->id);
                $invoiceQuery->whereHas('project', fn ($query) => $query->where('agency_id', $agency->id));
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
                'invoices' => $agency ? $invoiceQuery->orderByDesc('created_at')->take(5)->get() : collect(),
                'activeProjectsCount' => $activeProjectsCount,
                'clientsCount' => $clientsCount,
                'pendingApprovalsCount' => $pendingApprovalsCount,
                'unpaidInvoicesTotal' => $unpaidInvoicesTotal,
            ]);
        })->name('agency.home');

        // Projects listing & creation
        Route::get('/projects', function () {
            $agency = auth()->user()?->agency;
            if (! $agency) {
                return redirect()->route('agency.create');
            }
            $projects = Project::where('agency_id', $agency->id)->with('client')->orderByDesc('created_at')->get();
            return view('agency.projects.index', compact('projects'));
        })->name('projects.index');

        Route::get('/projects/create', function () {
            $agency = auth()->user()?->agency;
            if (! $agency) {
                return redirect()->route('agency.create');
            }
            $clients = Client::where('agency_id', $agency->id)->orderBy('name')->get();
            return view('agency.projects.create', compact('clients'));
        })->name('projects.create');

        Route::post('/projects', function () {
            $data = request()->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'client_id' => ['nullable', 'exists:clients,id'],
            ]);
            $agency = auth()->user()->agency;
            $project = Project::create([
                'agency_id' => $agency->id,
                'client_id' => $data['client_id'] ?? null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => 'in_progress',
            ]);
            return redirect()->route('projects.index')->with('success', 'Project created.');
        })->name('projects.store');

        Route::get('/projects/{project}', function (Project $project) {
            $agency = auth()->user()?->agency;
            if (! $agency || $project->agency_id !== $agency->id) {
                abort(403);
            }
            $deliverables = $project->deliverables()->orderByDesc('created_at')->get();
            return view('agency.projects.show', compact('project', 'deliverables'));
        })->name('projects.show');

        Route::post('/projects/{project}/deliverables', function (Project $project) {
            $agency = auth()->user()?->agency;
            if (! $agency || $project->agency_id !== $agency->id) {
                abort(403);
            }
            $data = request()->validate([
                'title' => ['required', 'string', 'max:255'],
                'file' => ['nullable', 'file', 'max:5120'],
            ]);
            $path = null;
            if (request()->hasFile('file')) {
                $path = request()->file('file')->store('deliverables');
            }
            Deliverable::create([
                'project_id' => $project->id,
                'uploaded_by' => auth()->id(),
                'title' => $data['title'],
                'file_path' => $path,
            ]);
            return redirect()->route('projects.show', $project->id)->with('success', 'Deliverable uploaded.');
        })->name('projects.deliverables.store');

        // Clients listing & creation
        Route::get('/clients', function () {
            $agency = auth()->user()?->agency;
            if (! $agency) {
                return redirect()->route('agency.create');
            }
            $clients = Client::where('agency_id', $agency->id)->orderBy('name')->get();
            return view('agency.clients.index', compact('clients'));
        })->name('clients.index');

        Route::get('/clients/create', function () {
            $agency = auth()->user()?->agency;
            if (! $agency) {
                return redirect()->route('agency.create');
            }
            return view('agency.clients.create');
        })->name('clients.create');

        Route::post('/clients', function () {
            $data = request()->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'company_name' => ['nullable', 'string', 'max:255'],
            ]);
            $agency = auth()->user()->agency;
            Client::create([
                'agency_id' => $agency->id,
                'user_id' => auth()->id(),
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'company_name' => $data['company_name'] ?? null,
            ]);
            return redirect()->route('clients.index')->with('success', 'Client added.');
        })->name('clients.store');

        Route::get('/agency/create', function () {
            return view('agency.create');
        })->name('agency.create');

        Route::get('/invoices/create', function () {
            $agency = auth()->user()?->agency;
            if (! $agency) {
                return redirect()->route('agency.create');
            }

            $projects = App\Models\projects::where('agency_id', $agency->id)->with('client')->orderByDesc('created_at')->get();
            return view('agency.invoices.create', compact('projects'));
        })->name('invoices.create');

        Route::post('/invoices', function () {
            $agency = auth()->user()?->agency;
            if (! $agency) {
                abort(403);
            }

            $data = request()->validate([
                'project_id' => ['required', 'exists:projects,id'],
                'amount' => ['required', 'numeric', 'min:0'],
                'due_date' => ['nullable', 'date'],
                'notes' => ['nullable', 'string'],
            ]);

            $project = App\Models\projects::find($data['project_id']);
            if (! $project || $project->agency_id !== $agency->id) {
                abort(403);
            }

            $invoice = App\Models\Invoice::create([
                'project_id' => $project->id,
                'amount' => $data['amount'],
                // Use enum-compatible status: 'sent' indicates invoice has been created/sent to client
                'status' => 'sent',
                'due_date' => $data['due_date'] ?? null,
            ]);

            // Optionally store notes to a related table or as metadata - omitted for now

            return redirect()->route('invoices.create')->with('success', 'Invoice created and queued for sending.');
        })->name('invoices.store');

        // Stripe webhook endpoint (controller)
        Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);
    });

    // Invoice show page - accessible to both agency owners and clients
    // Agency owners can view their invoices, clients can view invoices assigned to them to pay
    Route::get('/invoices/{invoice}', function (App\Models\Invoice $invoice) {
        $user = auth()->user();
        
        // Agency owners can view invoices from their agency
        if ($user->hasRole('agency_owner')) {
            $agency = $user->agency;
            if (!$agency || $invoice->project->agency_id !== $agency->id) {
                abort(403);
            }
        } 
        // Clients can view invoices from projects assigned to them
        elseif ($user->can('pay-invoice')) {
            // Allow clients to view invoices to pay them
        } else {
            abort(403);
        }
        
        return view('invoice_show', compact('invoice'));
    })->name('invoices.show');

    // Success redirect - mark invoice as paid and create payment record
    // Accessible to both agency owners and clients
    Route::get('/invoices/{invoice}/success', function (App\Models\Invoice $invoice, Request $request) {
        $sessionId = $request->query('session_id');
        
        // Update invoice status to paid if not already paid
        if ($invoice->status !== 'paid' && $sessionId) {
            $invoice->update(['status' => 'paid']);
            
            // Create payment record
            App\Models\Payment::create([
                'invoice_id' => $invoice->id,
                'amount_paid' => $invoice->amount,
                'payment_method' => 'card',
                'paid_at' => now(),
            ]);
        }
        
        return view('invoice_success', compact('invoice'));
    })->name('invoices.success');

    Route::middleware('role:client')->group(function () {
        Route::get('/client', function () {
            $client = auth()->user()?->client;
            if (! $client) {
                return view('client', ['clientExists' => false]);
            }

            $recentDeliverables = App\Models\deliverables::whereHas('project', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            })->orderByDesc('created_at')->take(5)->get();

            $recentInvoices = App\Models\Invoice::whereHas('project', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            })->orderByDesc('created_at')->take(5)->get();

            return view('client', [
                'clientExists' => true,
                'client' => $client,
                'recentDeliverables' => $recentDeliverables,
                'recentInvoices' => $recentInvoices,
            ]);
        })->name('client.home');
    });
});

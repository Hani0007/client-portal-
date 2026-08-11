<?php
use App\Http\Controllers\AgencyClientController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliverableController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StripeWebhookController;
use App\Models\clients as Client;
use App\Models\projects as Project;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Checkout endpoint (creates a Stripe Checkout Session) - accessible to agency owners and clients
    Route::post('/invoices/{invoice}/checkout', [PaymentController::class, 'checkout'])->name('invoices.checkout');

    Route::middleware('role:agency_owner')->group(function () {
        Route::get('/agency', [AgencyController::class, 'index'])->name('agency.home');

        // Projects listing & creation
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::post('/projects/{project}/deliverables', [ProjectController::class, 'storeDeliverable'])->name('projects.deliverables.store');

        Route::get('/clients', [AgencyClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/create', [AgencyClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [AgencyClientController::class, 'store'])->name('clients.store');

        Route::get('/agency/create', [AgencyController::class, 'create'])->name('agency.create');

        Route::get('/invoices/create', [ProjectController::class, 'createInvoice'])->name('invoices.create');

        Route::post('/invoices', [ProjectController::class, 'storeInvoice'])->name('invoices.store');
    });

    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/success', [PaymentController::class, 'success'])->name('invoices.success');

    Route::middleware('role:client')->group(function () {
        Route::get('/client', [ClientController::class, 'index'])->name('client.home');
        Route::post('/deliverables/{deliverable}/approve', [DeliverableController::class, 'approve'])->name('deliverables.approve');
        Route::post('/deliverables/{deliverable}/reject', [DeliverableController::class, 'reject'])->name('deliverables.reject');
    });
});

// Stripe webhook (must be outside auth middleware - Stripe calls this directly)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

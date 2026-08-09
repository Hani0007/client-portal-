<?php

namespace App\Http\Controllers;

use App\Models\clients as Client;
use App\Models\Invoice;
use App\Models\projects as Project;
use Illuminate\Http\Request;
use App\Models\User;

class AgencyController extends Controller
{
    /**
     * Display the agency dashboard
     */
    public function index()

    {
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

            $activeProjectsCount = Project::where('agency_id', $agency->id)
                ->where('status', 'in_progress')
                ->count();

            $clientsCount = Client::where('agency_id', $agency->id)->count();

            $pendingApprovalsCount = \App\Models\approvales::whereHas('deliverable.project', function ($q) use ($agency) {
                $q->where('agency_id', $agency->id);
            })->where('status', 'pending')->count();

            $unpaidInvoicesTotal = Invoice::whereHas('project', fn ($query) => $query->where('agency_id', $agency->id))
                ->where('status', '!=', 'paid')
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
    }

    /**
     * Show the form for creating a new agency
     */
    public function create()
    {
        return view('agency.create');
    }
}

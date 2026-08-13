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

            // Chart data
            $totalClientsCount = Client::where('agency_id', $agency->id)->count();
            $totalProjectsCount = Project::where('agency_id', $agency->id)->count();
            $inProgressProjectsCount = Project::where('agency_id', $agency->id)
                ->where('status', 'in_progress')
                ->count();
            $completedProjectsCount = Project::where('agency_id', $agency->id)
                ->where('status', 'completed')
                ->count();
            $rejectedProjectsCount = Project::where('agency_id', $agency->id)
                ->where('status', 'rejected')
                ->count();

            // Overall system preview data (last 6 months)
            $monthlyData = [];
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $currentMonth = date('n');
            $currentYear = date('Y');

            for ($i = 5; $i >= 0; $i--) {
                $month = ($currentMonth - $i + 11) % 12 + 1;
                $year = $currentMonth - $i < 1 ? $currentYear - 1 : $currentYear;

                $projectsCount = Project::where('agency_id', $agency->id)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();

                $clientsCount = Client::where('agency_id', $agency->id)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();

                $monthlyData[] = [
                    'month' => $months[$month - 1],
                    'projects' => $projectsCount,
                    'clients' => $clientsCount,
                ];
            }
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
            'totalClientsCount' => $totalClientsCount ?? 0,
            'totalProjectsCount' => $totalProjectsCount ?? 0,
            'inProgressProjectsCount' => $inProgressProjectsCount ?? 0,
            'completedProjectsCount' => $completedProjectsCount ?? 0,
            'rejectedProjectsCount' => $rejectedProjectsCount ?? 0,
            'monthlyData' => $monthlyData ?? [],
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

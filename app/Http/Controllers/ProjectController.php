<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Models\clients as Client;
use App\Models\deliverables as Deliverable;
use App\Models\Invoice;
use App\Models\projects as Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index()
    {
        $agency = auth()->user()?->agency;
        if (!$agency) {
            return redirect()->route('agency.create');
        }

        $projects = Project::with('client')
            ->where('agency_id', $agency->id)
            ->orderByDesc('created_at')
            ->get();

        return view('agency.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project
     */
    public function create()
    {
        $agency = auth()->user()?->agency;
        if (!$agency) {
            return redirect()->route('agency.create');
        }
        $clients = Client::where('agency_id', $agency->id)->orderBy('name')->get();
        return view('agency.projects.create', compact('clients'));
    }

    /**
     * Store a newly created project
     */
    public function store(StoreProjectRequest $request)
    {
        $agency = auth()->user()->agency;
        $project = Project::create([
            'agency_id' => $agency->id,
            'client_id' => $request->client_id ?? null,
            'name' => $request->name,
            'description' => $request->description ?? null,
            'status' => 'in_progress',
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created.');
    }

    /**
     * Display the specified project
     */
    public function show(Project $project)
    {
        $agency = auth()->user()?->agency;
        if (!$agency || $project->agency_id !== $agency->id) {
            abort(403);
        }

        $deliverables = Deliverable::where('project_id', $project->id)
            ->orderByDesc('created_at')
            ->get();

        return view('agency.projects.show', compact('project', 'deliverables'));
    }

    /**
     * Store a newly created deliverable
     */
    public function storeDeliverable(Request $request, Project $project)
    {
        $agency = auth()->user()?->agency;
        if (!$agency || $project->agency_id !== $agency->id) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:5120'],
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('deliverables');
        }

        $deliverable = Deliverable::create([
            'project_id' => $project->id,
            'uploaded_by' => auth()->id(),
            'title' => $data['title'],
            'file_path' => $path,
        ]);

        // Create pending approval if project has a client
        if ($project->client_id) {
            \App\Models\approvales::create([
                'deliverable_id' => $deliverable->id,
                'client_id' => $project->client_id,
                'status' => 'pending',
                'comments' => null,
            ]);
        }

        return redirect()->route('projects.show', $project->id)->with('success', 'Deliverable uploaded.');
    }

    /**
     * Store a newly created invoice
     */
    public function storeInvoice(StoreInvoiceRequest $request)
    {
        $agency = auth()->user()?->agency;
        if (!$agency) {
            abort(403);
        }

        $project = Project::find($request->project_id);
        if (!$project || $project->agency_id !== $agency->id) {
            abort(403);
        }

        if (!$project->client_id) {
            return redirect()->route('invoices.create')->with('error', 'Cannot create invoice for a project without an assigned client.');
        }

        Invoice::create([
            'project_id' => $project->id,
            'amount' => $request->amount,
            'status' => 'sent',
            'due_date' => $request->due_date ?? null,
        ]);

        return redirect()->route('invoices.create')->with('success', 'Invoice created and queued for sending.');
    }

    /**
     * Show the form for creating a new invoice
     */
    public function createInvoice()
    {
        $agency = auth()->user()?->agency;
        if (!$agency) {
            return redirect()->route('agency.create');
        }

        $projects = Project::where('agency_id', $agency->id)->with('client')->orderByDesc('created_at')->get();
        return view('agency.invoices.create', compact('projects'));
    }
}

<?php

namespace App\Console\Commands;

use App\Models\approvales;
use App\Models\projects as Project;
use Illuminate\Console\Command;

class UpdateProjectStatusesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update project statuses based on deliverable approvals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating project statuses...');

        $projects = Project::with('deliverables')->get();
        $updatedCount = 0;

        foreach ($projects as $project) {
            $deliverables = $project->deliverables;

            if ($deliverables->isEmpty()) {
                continue;
            }

            $allApproved = true;

            foreach ($deliverables as $deliverable) {
                $approval = approvales::where('deliverable_id', $deliverable->id)->first();
                if (!$approval || $approval->status !== 'approved') {
                    $allApproved = false;
                    break;
                }
            }

            if ($allApproved && $project->status !== 'completed') {
                $project->update(['status' => 'completed']);
                $updatedCount++;
                $this->info("Updated project: {$project->name} to completed");
            }
        }

        $this->info("Updated {$updatedCount} projects to completed status.");
        $this->info('Project status update complete!');
    }
}

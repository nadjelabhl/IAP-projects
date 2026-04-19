<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectArchive;
use App\Models\Expense;
use App\Models\TodoTask;
use App\Models\OdsRecord;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class ArchiveService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Archive a project with a full snapshot
     * Stores complete project state including all related data
     *
     * @param Project $project
     * @param string $reason
     * @param int|null $archivedBy
     * @return ProjectArchive|null
     */
    public function archiveProject(Project $project, string $reason, ?int $archivedBy = null): ?ProjectArchive
    {
        // Only completed projects can be archived
        if ($project->status !== 'Termine') {
            return null;
        }

        DB::beginTransaction();
        try {
            // Gather complete project data snapshot
            $snapshot = $this->createProjectSnapshot($project);

            // Create archive record
            $archive = ProjectArchive::create([
                'project_id' => $project->id,
                'archived_by' => $archivedBy,
                'archived_at' => now(),
                'reason' => $reason,
                'snapshot' => json_encode($snapshot),
                'project_title' => $project->title,
                'school_id' => $project->school_id,
                'budget' => $project->budget,
                'total_spent' => $snapshot['total_spent'],
                'status' => $project->status,
            ]);

            // Mark project as closed
            $project->closed_at = now();
            $project->save();

            // Notify stakeholders about archiving
            $this->notificationService->notifyProjectArchived($project);

            DB::commit();
            return $archive;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    /**
     * Create a complete snapshot of project data
     *
     * @param Project $project
     * @return array
     */
    protected function createProjectSnapshot(Project $project): array
    {
        $budgetService = new BudgetService($this->notificationService);

        return [
            // Basic project information
            'id' => $project->id,
            'title' => $project->title,
            'nature_id' => $project->nature_id,
            'nature_name' => $project->nature ? $project->nature->name : null,
            'type' => $project->type,
            'school_id' => $project->school_id,
            'school_name' => $project->school ? $project->school->name : null,
            'created_by' => $project->created_by,
            'creator_name' => $project->creator ? $project->creator->name : null,
            'budget' => $project->budget,
            'duration_months' => $project->duration_months,
            'start_year' => $project->start_year,
            'end_year' => $project->end_year,
            'address' => $project->address,
            'description' => $project->description,
            'status' => $project->status,
            'juriste_id' => $project->juriste_id,
            'juriste_name' => $project->juriste ? $project->juriste->name : null,
            'chef_projet_id' => $project->chef_projet_id,
            'chef_projet_name' => $project->chef_projet ? $project->chef_projet->name : null,
            'chef_access_unlocked' => $project->chef_access_unlocked,
            'dg_consulted_at' => $project->dg_consulted_at ? $project->dg_consulted_at->format('Y-m-d H:i:s') : null,
            'budget_alert_sent' => $project->budget_alert_sent,
            'closed_at' => $project->closed_at ? $project->closed_at->format('Y-m-d H:i:s') : null,
            'created_at' => $project->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $project->updated_at->format('Y-m-d H:i:s'),

            // Budget information
            'total_spent' => $budgetService->calculateTotalSpent($project),
            'remaining_budget' => $budgetService->calculateRemainingBudget($project),
            'budget_consumption_percent' => $budgetService->calculateBudgetConsumption($project),

            // Related expenses
            'expenses' => $project->expenses->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'expense_date' => $expense->expense_date->format('Y-m-d'),
                    'entered_by' => $expense->entered_by,
                    'entered_by_name' => $expense->enteredBy ? $expense->enteredBy->name : null,
                    'created_at' => $expense->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray(),

            // Related tasks
            'tasks' => $project->tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'percentage' => $task->percentage,
                    'is_completed' => $task->is_completed,
                    'completed_at' => $task->completed_at ? $task->completed_at->format('Y-m-d H:i:s') : null,
                    'created_by' => $task->created_by,
                    'created_by_name' => $task->createdBy ? $task->createdBy->name : null,
                    'sort_order' => $task->sort_order,
                    'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray(),

            // Related ODS records
            'ods_records' => $project->odsRecords->map(function ($ods) {
                return [
                    'id' => $ods->id,
                    'emitted_by' => $ods->emitted_by,
                    'emitted_by_name' => $ods->emitter ? $ods->emitter->name : null,
                    'emitted_at' => $ods->emitted_at->format('Y-m-d H:i:s'),
                    'reason' => $ods->reason,
                    'status' => $ods->status,
                    'cancelled_by' => $ods->cancelled_by,
                    'cancelled_by_name' => $ods->cancelledBy ? $ods->cancelledBy->name : null,
                    'cancelled_at' => $ods->cancelled_at ? $ods->cancelled_at->format('Y-m-d H:i:s') : null,
                ];
            })->toArray(),

            // Task completion summary
            'task_summary' => [
                'total_tasks' => $project->tasks->count(),
                'completed_tasks' => $project->tasks->where('is_completed', true)->count(),
                'total_percentage' => $project->tasks->sum('percentage'),
            ],

            // Expense summary
            'expense_summary' => [
                'total_expenses' => $project->expenses->count(),
                'total_amount' => $project->expenses->sum('amount'),
            ],
        ];
    }

    /**
     * Get all archived projects
     *
     * @param int|null $schoolId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getArchivedProjects(?int $schoolId = null)
    {
        $query = ProjectArchive::with(['project', 'archiver', 'school'])
            ->orderBy('archived_at', 'desc');

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->get();
    }

    /**
     * Get archived project by ID
     *
     * @param int $archiveId
     * @return ProjectArchive|null
     */
    public function getArchiveById(int $archiveId): ?ProjectArchive
    {
        return ProjectArchive::with(['project', 'archiver', 'school'])
            ->find($archiveId);
    }

    /**
     * Restore an archived project (if needed)
     * Note: This would require additional business logic
     *
     * @param int $archiveId
     * @param int|null $restoredBy
     * @return bool
     */
    public function restoreArchive(int $archiveId, ?int $restoredBy = null): bool
    {
        $archive = ProjectArchive::find($archiveId);
        
        if (!$archive) {
            return false;
        }

        $project = Project::find($archive->project_id);
        
        if (!$project) {
            return false;
        }

        DB::beginTransaction();
        try {
            // Reopen the project
            $project->closed_at = null;
            $project->status = 'En Cours';
            $project->save();

            // Delete the archive record
            $archive->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Get archive statistics
     *
     * @param int|null $schoolId
     * @return array
     */
    public function getArchiveStatistics(?int $schoolId = null): array
    {
        $query = ProjectArchive::query();

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $totalArchives = $query->count();
        $totalBudget = $query->sum('budget');
        $totalSpent = $query->sum('total_spent');

        return [
            'total_archives' => $totalArchives,
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'total_remaining' => $totalBudget - $totalSpent,
        ];
    }

    /**
     * Delete old archives (cleanup task)
     *
     * @param int $monthsOld
     * @return int Number of archives deleted
     */
    public function deleteOldArchives(int $monthsOld = 24): int
    {
        $cutoffDate = now()->subMonths($monthsOld);

        return ProjectArchive::where('archived_at', '<', $cutoffDate)
            ->delete();
    }
}

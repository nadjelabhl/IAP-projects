<?php

namespace App\Services;

use App\Models\Project;
use App\Models\OdsRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OdsService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Check if a project can emit an ODS
     * ODS is independent from tasks - it can be emitted based on project status
     *
     * @param Project $project
     * @return bool
     */
    public function canEmitODS(Project $project): bool
    {
        // ODS can be emitted for projects in "En Cours" or "Terminé" status
        // It's independent from task completion percentage
        return in_array($project->status, ['En Cours', 'Termine']);
    }

    /**
     * Emit an ODS for a project
     * This unlocks chef de projet access to manage expenses
     *
     * @param Project $project
     * @param string $reason
     * @param int|null $emittedBy
     * @return OdsRecord|null
     */
    public function emitODS(Project $project, string $reason, ?int $emittedBy = null): ?OdsRecord
    {
        if (!$this->canEmitODS($project)) {
            return null;
        }

        DB::beginTransaction();
        try {
            // Create ODS record
            $ods = OdsRecord::create([
                'project_id' => $project->id,
                'emitted_by' => $emittedBy,
                'emitted_at' => now(),
                'reason' => $reason,
                'status' => 'active',
            ]);

            // Unlock chef de projet access if assigned
            if ($project->chef_projet_id) {
                $project->chef_access_unlocked = true;
                $project->save();

                // Notify chef de projet that access is unlocked
                $this->notificationService->notifyChefAccessUnlocked($project);
            }

            // Notify relevant users about ODS emission
            $this->notificationService->notifyODSEmitted($project);

            DB::commit();
            return $ods;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    /**
     * Get all ODS records for a project
     *
     * @param int $projectId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProjectODS(int $projectId)
    {
        return OdsRecord::where('project_id', $projectId)
            ->with('emitter')
            ->orderBy('emitted_at', 'desc')
            ->get();
    }

    /**
     * Get active ODS for a project
     *
     * @param int $projectId
     * @return OdsRecord|null
     */
    public function getActiveODS(int $projectId): ?OdsRecord
    {
        return OdsRecord::where('project_id', $projectId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Cancel an ODS record
     *
     * @param int $odsId
     * @param int|null $cancelledBy
     * @return bool
     */
    public function cancelODS(int $odsId, ?int $cancelledBy = null): bool
    {
        $ods = OdsRecord::find($odsId);
        
        if (!$ods) {
            return false;
        }

        $ods->status = 'cancelled';
        $ods->cancelled_by = $cancelledBy;
        $ods->cancelled_at = now();
        $ods->save();

        return true;
    }

    /**
     * Check if chef de projet has access to manage expenses for a project
     * Access is granted when ODS is emitted and chef_access_unlocked is true
     *
     * @param Project $project
     * @param int $chefId
     * @return bool
     */
    public function hasChefAccess(Project $project, int $chefId): bool
    {
        // Chef must be assigned to the project
        if ($project->chef_projet_id !== $chefId) {
            return false;
        }

        // Access must be unlocked via ODS
        if (!$project->chef_access_unlocked) {
            return false;
        }

        // There must be at least one active ODS
        $activeODS = $this->getActiveODS($project->id);
        if (!$activeODS) {
            return false;
        }

        return true;
    }
}

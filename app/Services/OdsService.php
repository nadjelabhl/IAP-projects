<?php

namespace App\Services;

use App\Models\OdsRecord;
use App\Models\Project;
use App\Models\TodoTask;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OdsService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Vérifie si le juriste peut émettre l'ODS de Démarrage.
     * Condition : toutes les phases cochées et leur somme = 100%.
     */
    public function canEmitDemarrage(Project $project): bool
    {
        $total = TodoTask::where('project_id', $project->id)->sum('percentage');
        $completed = TodoTask::where('project_id', $project->id)
            ->where('is_completed', true)
            ->sum('percentage');

        return $total >= 100 && $completed >= 100;
    }

    /**
     * Émet l'ODS de Démarrage.
     * Change le statut du projet en 'En Cours', débloque l'accès Chef.
     */
    public function emitDemarrage(Project $project, User $juriste, ?string $notes = null): ?OdsRecord
    {
        if (!$this->canEmitDemarrage($project)) {
            return null;
        }

        DB::beginTransaction();
        try {
            $lastStep = TodoTask::where('project_id', $project->id)
                ->orderBy('sort_order', 'desc')
                ->first();

            $ods = OdsRecord::create([
                'project_id'          => $project->id,
                'issued_by'           => $juriste->id,
                'type_ods'            => 'Demarrage',
                'notes'               => $notes,
                'ods_record_pdf_path' => $lastStep?->todo_tasks_pdf_path,
                'issued_at'           => now(),
            ]);

            $project->update([
                'status'               => 'En Cours',
                'chef_access_unlocked' => true,
                'started_at'           => now(),
            ]);

            // Notifier le Chef de Projet
            if ($project->chef_projet_id) {
                $this->notificationService->notifyChefAccessUnlocked($project);
            }

            // Notifier Directeur École
            $this->notificationService->notifyOdsDemarrage($project);

            DB::commit();
            return $ods;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    /**
     * Émet un ODS d'Arrêt (incident technique).
     */
    public function emitArret(Project $project, User $juriste, ?string $notes = null, ?string $pdfPath = null): ?OdsRecord
    {
        if ($project->status !== 'En Cours') {
            return null;
        }

        $ods = OdsRecord::create([
            'project_id'          => $project->id,
            'issued_by'           => $juriste->id,
            'type_ods'            => 'Arret',
            'notes'               => $notes,
            'ods_record_pdf_path' => $pdfPath,
            'issued_at'           => now(),
        ]);

        $this->notificationService->notifyOdsArret($project);

        return $ods;
    }

    /**
     * Émet un ODS de Reprise (incident résolu).
     */
    public function emitReprise(Project $project, User $juriste, ?string $notes = null, ?string $pdfPath = null): ?OdsRecord
    {
        if ($project->status !== 'En Cours') {
            return null;
        }

        $ods = OdsRecord::create([
            'project_id'          => $project->id,
            'issued_by'           => $juriste->id,
            'type_ods'            => 'Reprise',
            'notes'               => $notes,
            'ods_record_pdf_path' => $pdfPath,
            'issued_at'           => now(),
        ]);

        $this->notificationService->notifyOdsReprise($project);

        return $ods;
    }

    public function getProjectOds(int $projectId)
    {
        return OdsRecord::where('project_id', $projectId)
            ->with('issuedBy')
            ->orderBy('issued_at', 'desc')
            ->get();
    }

    public function hasChefAccess(Project $project, int $chefId): bool
    {
        return $project->chef_projet_id === $chefId
            && $project->chef_access_unlocked
            && $project->status === 'En Cours';
    }
}

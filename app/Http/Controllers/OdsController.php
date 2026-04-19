<?php

namespace App\Http\Controllers;

use App\Models\OdsRecord;
use App\Models\Project;
use App\Services\OdsService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OdsController extends Controller
{
    protected OdsService $odsService;
    protected NotificationService $notificationService;

    public function __construct(OdsService $odsService, NotificationService $notificationService)
    {
        $this->odsService = $odsService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display ODS records for a project
     *
     * @param Project $project
     * @return \Illuminate\View\View
     */
    public function index(Project $project)
    {
        $user = auth()->user();

        // Vérifier l'accès au projet
        if (!$this->canAccessProject($user, $project)) {
            abort(403, 'Accès refusé à ce projet');
        }

        $odsRecords = $this->odsService->getProjectODS($project->id);
        $activeODS = $this->odsService->getActiveODS($project->id);

        return view('ods.index', [
            'project' => $project,
            'odsRecords' => $odsRecords,
            'activeODS' => $activeODS,
            'canEmitODS' => $this->odsService->canEmitODS($project),
            'user' => $user,
        ]);
    }

    /**
     * Show form to emit ODS
     *
     * @param Project $project
     * @return \Illuminate\View\View
     */
    public function create(Project $project)
    {
        $user = auth()->user();

        // Seul le DG, Directeur d'École, ou Juriste peuvent émettre ODS
        if (!in_array($user->role, ['dg', 'directeur_ecole', 'juriste'])) {
            abort(403, 'Accès refusé');
        }

        // Vérifier l'accès au projet
        if (!$this->canAccessProject($user, $project)) {
            abort(403, 'Accès refusé à ce projet');
        }

        if (!$this->odsService->canEmitODS($project)) {
            return back()->with('error', 'ODS ne peut être émis que pour les projets en cours ou terminés');
        }

        return view('ods.create', [
            'project' => $project,
            'user' => $user,
        ]);
    }

    /**
     * Emit ODS for a project
     *
     * @param Request $request
     * @param Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Project $project)
    {
        $user = auth()->user();

        // Seul le DG, Directeur d'École, ou Juriste peuvent émettre ODS
        if (!in_array($user->role, ['dg', 'directeur_ecole', 'juriste'])) {
            abort(403, 'Accès refusé');
        }

        // Vérifier l'accès au projet
        if (!$this->canAccessProject($user, $project)) {
            abort(403, 'Accès refusé à ce projet');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $ods = $this->odsService->emitODS($project, $request->reason, $user->id);

        if ($ods) {
            return redirect()->route('ods.index', $project)
                ->with('success', 'ODS émis avec succès. Accès chef de projet débloqué.');
        }

        return back()->with('error', 'Erreur lors de l\'émission de l\'ODS');
    }

    /**
     * Cancel an ODS record
     *
     * @param Request $request
     * @param OdsRecord $ods
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request, OdsRecord $ods)
    {
        $user = auth()->user();

        // Seul le DG ou Directeur d'École peuvent annuler ODS
        if (!in_array($user->role, ['dg', 'directeur_ecole'])) {
            abort(403, 'Accès refusé');
        }

        $project = Project::find($ods->project_id);

        // Vérifier l'accès au projet
        if (!$this->canAccessProject($user, $project)) {
            abort(403, 'Accès refusé à ce projet');
        }

        if ($this->odsService->cancelODS($ods->id, $user->id)) {
            return back()->with('success', 'ODS annulé avec succès');
        }

        return back()->with('error', 'Erreur lors de l\'annulation de l\'ODS');
    }

    /**
     * Check if user can access project
     *
     * @param $user
     * @param Project $project
     * @return bool
     */
    private function canAccessProject($user, Project $project): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'dg') return true;
        if ($user->role === 'assistant_dg') return true;

        if ($user->role === 'directeur_ecole') {
            return $project->school_id === $user->school_id;
        }

        if ($user->role === 'juriste') {
            return $project->juriste_id === $user->id;
        }

        if ($user->role === 'chef_projet') {
            return $project->chef_projet_id === $user->id;
        }

        return false;
    }
}

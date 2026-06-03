<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Liste des écoles (Admin)
     */
    public function index()
    {
        $schools = School::withCount(['users', 'projects'])
            ->orderBy('name_school')
            ->paginate(20);

        return view('admin.schools.index', [
            'schools' => $schools
        ]);
    }

    /**
     * Créer une nouvelle école
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:schools,name_school',
            'location' => 'nullable|string|max:150',
            'annual_budget' => 'nullable|numeric|min:0',
        ]);

        School::create([
            'name_school'   => $request->name,
            'location'      => $request->location,
            'annual_budget' => $request->annual_budget ?? 0,
        ]);

        return redirect()->back()->with('success', 'École créée avec succès');
    }

    /**
     * Mettre à jour une école
     */
    public function update(Request $request, School $school)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:schools,name_school,' . $school->id . ',id_school',
            'location' => 'nullable|string|max:150',
            'annual_budget' => 'nullable|numeric|min:0',
        ]);

        $school->update([
            'name_school'   => $request->name,
            'location'      => $request->location,
            'annual_budget' => $request->annual_budget ?? $school->annual_budget,
        ]);

        return redirect()->back()->with('success', 'École mise à jour');
    }

    /**
     * Supprimer une école
     */
    public function destroy(School $school)
    {
        // Vérifier qu'il n'y a pas de projets actifs
        $activeProjects = $school->projects()->where('status', '!=', 'Termine')->count();

        if ($activeProjects > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer une école avec des projets actifs');
        }

        // Vérifier qu'il n'y a pas d'utilisateurs (sauf admin)
        $usersCount = $school->users()->where('role', '!=', 'admin')->count();

        if ($usersCount > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer une école avec des utilisateurs');
        }

        $school->delete();

        return redirect()->back()->with('success', 'École supprimée');
    }

    /**
     * Liste des utilisateurs d'une école (pour affectation RH)
     */
    public function users(School $school)
    {
        $juristes = $school->users()->where('role', 'juriste')->get();
        $chefs = $school->users()->where('role', 'chef_projet')->get();

        return response()->json([
            'juristes' => $juristes,
            'chefs' => $chefs,
        ]);
    }
}
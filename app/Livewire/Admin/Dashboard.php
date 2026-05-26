<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Project;
use App\Models\School;
use App\Models\ProjectNature;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app')]
class Dashboard extends Component
{
    use WithPagination;

    // Modale création/édition
    public bool   $createOpen   = false;
    public bool   $editOpen     = false;
    public bool   $deleteOpen   = false;
    public ?int   $editingId    = null;
    public ?int   $deletingId   = null;

    // Champs formulaire
    public string $uName      = '';
    public string $uEmail     = '';
    public string $uPassword  = '';
    public string $uRole      = 'chef_projet';
    public ?int   $uSchoolId  = null;
    public bool   $uIsActive  = true;

    protected function rules(): array
    {
        $uniqueRule = $this->editingId
            ? 'unique:users,email,' . $this->editingId
            : 'unique:users,email';
        return [
            'uName'     => 'required|string|min:2|max:100',
            'uEmail'    => "required|email|$uniqueRule",
            'uPassword' => $this->editingId ? 'nullable|min:6' : 'required|min:6',
            'uRole'     => 'required|in:admin,assistant_dg,dg,directeur_ecole,juriste,chef_projet',
            'uSchoolId' => 'nullable',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->createOpen = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->uName     = $user->name;
        $this->uEmail    = $user->email;
        $this->uPassword = '';
        $this->uRole     = $user->role;
        $this->uSchoolId = $user->school_id;
        $this->uIsActive = $user->is_active;
        $this->editOpen  = true;
    }

    public function openDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->deleteOpen = true;
    }

    public function saveCreate(): void
    {
        $this->validate();
        User::create([
            'name'      => $this->uName,
            'email'     => $this->uEmail,
            'password'  => Hash::make($this->uPassword),
            'role'      => $this->uRole,
            'school_id' => $this->uSchoolId,
            'is_active' => $this->uIsActive,
        ]);
        $this->createOpen = false;
        $this->resetForm();
        session()->flash('success', 'Compte créé avec succès.');
    }

    public function saveEdit(): void
    {
        $this->validate();
        $user = User::findOrFail($this->editingId);
        $data = [
            'name'      => $this->uName,
            'email'     => $this->uEmail,
            'role'      => $this->uRole,
            'school_id' => $this->uSchoolId,
            'is_active' => $this->uIsActive,
        ];
        if ($this->uPassword) {
            $data['password'] = Hash::make($this->uPassword);
        }
        $user->update($data);
        $this->editOpen   = false;
        $this->editingId  = null;
        $this->resetForm();
        session()->flash('success', 'Compte mis à jour.');
    }

    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
    }

    public function confirmDelete(): void
    {
        if ($this->deletingId) {
            User::findOrFail($this->deletingId)->delete();
        }
        $this->deleteOpen = false;
        $this->deletingId = null;
        session()->flash('success', 'Compte supprimé.');
    }

    private function resetForm(): void
    {
        $this->uName     = '';
        $this->uEmail    = '';
        $this->uPassword = '';
        $this->uRole     = 'chef_projet';
        $this->uSchoolId = null;
        $this->uIsActive = true;
        $this->editingId = null;
        $this->deletingForm();
    }

    private function deletingForm(): void
    {
        $this->resetErrorBag();
    }

    public function render()
    {
        $projetsParEcole = School::withCount('projects')->get();
        return view('livewire.admin.dashboard', [
            'totalUsers'      => User::count(),
            'totalProjects'   => Project::count(),
            'totalSchools'    => School::count(),
            'projetsParEcole' => $projetsParEcole,
            'users'           => User::with('school')->orderBy('name')->paginate(10),
            'schools'         => School::orderBy('name')->get(),
            'deletingUser'    => $this->deletingId ? User::find($this->deletingId) : null,
        ]);
    }
}

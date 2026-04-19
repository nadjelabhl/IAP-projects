<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\ProjectNature;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ManageNatures extends Component
{
    use WithPagination;

    public $name;
    public $nature_id;
    public $is_active = true;
    public $isModalOpen = false;
    public $deleteModalOpen = false;
    public $natureToDelete;

    protected $rules = [
        'name' => 'required|string|min:3|max:100|unique:project_natures,name',
    ];

    protected $messages = [
        'name.required' => 'Le nom de la nature est obligatoire.',
        'name.unique' => 'Cette nature existe déjà.',
        'name.min' => 'Le nom doit contenir au moins 3 caractères.',
    ];

    public function create()
    {
        $this->validate();

        ProjectNature::create([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Nature de projet créée avec succès.');
        $this->closeModal();
        $this->reset(['name', 'is_active']);
    }

    public function edit($id)
    {
        $nature = ProjectNature::findOrFail($id);
        $this->nature_id = $nature->id;
        $this->name = $nature->name;
        $this->is_active = $nature->is_active;
        $this->openModal();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:100|unique:project_natures,name,' . $this->nature_id,
        ]);

        $nature = ProjectNature::findOrFail($this->nature_id);
        $nature->update([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Nature de projet mise à jour.');
        $this->closeModal();
        $this->reset(['name', 'is_active']);
    }

    public function confirmDelete($id)
    {
        $this->natureToDelete = ProjectNature::findOrFail($id);
        $this->deleteModalOpen = true;
    }

    public function delete()
    {
        if ($this->natureToDelete) {
            // Vérifier si la nature est utilisée par des projets
            if ($this->natureToDelete->projects()->count() > 0) {
                // soft delete - désactiver plutôt que supprimer
                $this->natureToDelete->update(['is_active' => false]);
                session()->flash('message', 'Nature désactivée (utilisée par des projets).');
            } else {
                $this->natureToDelete->delete();
                session()->flash('message', 'Nature de projet supprimée.');
            }
        }
        $this->deleteModalOpen = false;
        $this->natureToDelete = null;
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['name', 'nature_id', 'is_active']);
    }

    public function render()
    {
        return view('livewire.admin.manage-natures', [
            'natures' => ProjectNature::orderBy('name')->paginate(10)
        ]);
    }
}
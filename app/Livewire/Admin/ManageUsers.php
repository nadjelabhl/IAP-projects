<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ManageUsers extends Component
{
    use WithPagination;

    // Champs du formulaire
    public $name;
    public $email;
    public $password;
    public $role = 'chef_projet';
    public $school_id;
    public $user_id;
    public $is_active = true;

    public $isModalOpen = false;
    public $deleteModalOpen = false;
    public $userToDelete;

    protected $rules = [
        'name' => 'required|string|min:3|max:100',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'role' => 'required|in:admin,assistant_dg,dg,directeur_ecole,juriste,chef_projet',
        'school_id' => 'required_if:role,directeur_ecole,juriste,chef_projet|nullable',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Le nom est obligatoire.',
        'email.required' => 'L\'email est obligatoire.',
        'email.email' => 'Format d\'email invalide.',
        'email.unique' => 'Cet email existe déjà.',
        'password.required' => 'Le mot de passe est obligatoire.',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        'school_id.required_if' => 'L\'école est requise pour ce rôle.',
    ];

    public function create()
    {
        $this->validate();

        User::create([
            'name'      => $this->name,
            'email'     => $this->email,
            'password'  => Hash::make($this->password),
            'role'      => $this->role,
            'school_id' => $this->school_id,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Utilisateur créé avec succès.');
        $this->closeModal();
        $this->reset(['name', 'email', 'password', 'school_id', 'is_active']);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->school_id = $user->school_id;
        $this->is_active = $user->is_active;
        $this->openModal();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,assistant_dg,dg,directeur_ecole,juriste,chef_projet',
            'school_id' => 'required_if:role,directeur_ecole,juriste,chef_projet|nullable',
        ]);

        $user = User::findOrFail($this->user_id);
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'school_id' => $this->school_id,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        session()->flash('message', 'Utilisateur mis à jour.');
        $this->closeModal();
        $this->reset(['name', 'email', 'password', 'school_id', 'is_active']);
    }

    public function confirmDelete($id)
    {
        $this->userToDelete = User::findOrFail($id);
        $this->deleteModalOpen = true;
    }

    public function delete()
    {
        if ($this->userToDelete) {
            // Soft delete plutôt que supprimer définitivement
            $this->userToDelete->update(['is_active' => false]);
            session()->flash('message', 'Utilisateur désactivé.');
        }
        $this->deleteModalOpen = false;
        $this->userToDelete = null;
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['name', 'email', 'password', 'user_id', 'school_id', 'is_active']);
    }

    public function render()
    {
        return view('livewire.admin.manage-users', [
            'users' => User::orderBy('name')->paginate(15),
            'schools' => School::all(),
        ]);
    }
}
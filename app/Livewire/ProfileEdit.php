<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProfileEdit extends Component
{
    public string $name                  = '';
    public string $currentPassword       = '';
    public string $newPassword           = '';
    public string $newPassword_confirmation = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
    }

    public function saveProfile(): void
    {
        $this->validate(['name' => 'required|string|max:255']);

        Auth::user()->update(['name' => $this->name]);

        session()->flash('success', 'Profil mis à jour.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword'     => ['required', 'confirmed', Password::defaults()],
        ], [
            'newPassword.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        if (!Hash::check($this->currentPassword, Auth::user()->password)) {
            $this->addError('currentPassword', 'Mot de passe actuel incorrect.');
            return;
        }

        Auth::user()->update(['password' => Hash::make($this->newPassword)]);

        $this->reset(['currentPassword', 'newPassword', 'newPassword_confirmation']);
        session()->flash('successPwd', 'Mot de passe modifié avec succès.');
    }

    public function render()
    {
        return view('livewire.profile-edit');
    }
}

<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{
    /**
     * Utilisation du Form Object pour gérer la validation et l'authentification.
     */
    public LoginForm $form;

    /**
     * Méthode de connexion.
     */
    public function login()
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Rendu avec le layout 'guest' (souvent utilisé pour l'auth).
     */
    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
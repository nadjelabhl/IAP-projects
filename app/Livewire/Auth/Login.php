<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{
    public LoginForm $form;

    public string $authError      = '';
    public int    $throttleSeconds = 0;

    public string $resetEmail     = '';
    public string $resetStatus    = '';
    public string $resetError     = '';

    public function login(): void
    {
        $this->authError      = '';
        $this->throttleSeconds = 0;

        $key = Str::transliterate(Str::lower($this->form->email) . '|' . request()->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->throttleSeconds = RateLimiter::availableIn($key);
            return;
        }

        if (empty(trim($this->form->email)) || empty($this->form->password)) {
            $this->authError = 'Veuillez renseigner votre email et votre mot de passe.';
            return;
        }

        if (!Auth::attempt(['email' => $this->form->email, 'password' => $this->form->password], $this->form->remember)) {
            RateLimiter::hit($key);
            $this->authError = 'Identifiants incorrects. Vérifiez votre email et votre mot de passe.';
            return;
        }

        if (!Auth::user()->is_active) {
            Auth::logout();
            $this->authError = 'Votre compte est désactivé. Contactez l\'administrateur.';
            return;
        }

        RateLimiter::clear($key);
        Session::regenerate();
        $this->redirect(route('dashboard'), navigate: false);
    }

    public function sendResetLink(): void
    {
        $this->resetError  = '';
        $this->resetStatus = '';

        $this->validate(
            ['resetEmail' => 'required|email'],
            ['resetEmail.required' => 'L\'adresse email est obligatoire.', 'resetEmail.email' => 'Format d\'email invalide.']
        );

        $status = Password::sendResetLink(['email' => $this->resetEmail]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->resetStatus = 'Lien envoyé ! Vérifiez votre boîte de réception.';
            $this->resetEmail  = '';
        } else {
            $this->resetError = 'Aucun compte trouvé avec cet email.';
        }
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}

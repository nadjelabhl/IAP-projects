<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'Veuillez saisir une adresse email valide.',
        ]);

        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', 'Aucun compte trouvé avec cet email.');
            return;
        }

        $this->reset('email');
        session()->flash('status', 'Un lien de réinitialisation a été envoyé à votre adresse email. Vérifiez votre boîte de réception.');
    }
}; ?>

<div style="min-height: 100vh; display: flex; font-family: 'Poppins', sans-serif; overflow: hidden;">

<style>
@keyframes fadeInRight {
    from { opacity: 0; transform: translateX(40px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes fadeInLeft {
    from { opacity: 0; transform: translateX(-40px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes pulse-ring {
    0%   { transform: translate(-50%, -50%) scale(0.8); opacity: 0.6; }
    100% { transform: translate(-50%, -50%) scale(1.4); opacity: 0; }
}
@keyframes rotate-slow {
    from { transform: translate(-50%, -50%) rotate(0deg); }
    to   { transform: translate(-50%, -50%) rotate(360deg); }
}
@keyframes twinkle {
    0%, 100% { opacity: 0.2; transform: scale(1); }
    50%       { opacity: 0.8; transform: scale(1.3); }
}
</style>

    <!-- Left Side -->
    <div style="flex: 1; background: linear-gradient(145deg, #060d1a 0%, #0a1628 35%, #0d2040 70%, #0a1a35 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 60px; position: relative; overflow: hidden;">

        <div style="position: absolute; top: 50%; left: 50%; width: 700px; height: 700px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.04); animation: rotate-slow 30s linear infinite;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 550px; height: 550px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.06); animation: rotate-slow 20s linear infinite reverse;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 400px; height: 400px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.08);"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 280px; height: 280px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.2); animation: pulse-ring 3s ease-out infinite;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 280px; height: 280px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.15); animation: pulse-ring 3s ease-out infinite 1s;"></div>

        <div style="position: absolute; top: 18%; left: 12%; width: 3px; height: 3px; background: #f97316; border-radius: 50%; animation: twinkle 2.5s ease-in-out infinite;"></div>
        <div style="position: absolute; top: 25%; left: 80%; width: 4px; height: 4px; background: #f97316; border-radius: 50%; animation: twinkle 3s ease-in-out infinite 0.5s;"></div>
        <div style="position: absolute; top: 72%; left: 10%; width: 3px; height: 3px; background: #60a5fa; border-radius: 50%; animation: twinkle 2s ease-in-out infinite 1s;"></div>
        <div style="position: absolute; top: 80%; left: 75%; width: 5px; height: 5px; background: #f97316; border-radius: 50%; animation: twinkle 3.5s ease-in-out infinite 0.3s;"></div>

        <div style="position: relative; z-index: 1; text-align: center; max-width: 400px; animation: fadeInLeft 0.9s ease forwards;">
            <div style="position: relative; display: inline-block; margin-bottom: 36px;">
                <div style="position: absolute; inset: -25px; background: radial-gradient(circle, rgba(249,115,22,0.08) 0%, transparent 70%); border-radius: 50%;"></div>
                <img src="{{ asset('images/sonatrach-logo.png.png') }}" alt="IAP"
                    style="width: 130px; height: 130px; object-fit: contain; position: relative; z-index: 1; filter: brightness(0) saturate(100%) invert(55%) sepia(90%) saturate(800%) hue-rotate(340deg) brightness(105%);">
            </div>
            <h1 style="font-size: 38px; font-weight: 900; margin: 0; line-height: 1.2; letter-spacing: -1px;">
                <span style="color: white;">INSTITUT ALGÉRIEN DU PÉTROLE</span>
            </h1>
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin: 20px auto;">
                <div style="width: 30px; height: 1px; background: linear-gradient(90deg, transparent, #f97316);"></div>
                <div style="width: 6px; height: 6px; background: #f97316; border-radius: 50%;"></div>
                <div style="width: 30px; height: 1px; background: linear-gradient(90deg, #f97316, transparent);"></div>
            </div>
            <p style="font-size: 12px; color: rgba(255,255,255,0.4); margin: 0; letter-spacing: 2px; text-transform: uppercase;">
                Plateforme de Gestion des Projets
            </p>
            <div style="margin-top: 48px; display: inline-flex; align-items: center; gap: 10px; background: rgba(249,115,22,0.06); border: 1px solid rgba(249,115,22,0.15); border-radius: 30px; padding: 10px 20px;">
                <div style="width: 6px; height: 6px; background: #f97316; border-radius: 50%; box-shadow: 0 0 8px rgba(249,115,22,0.6);"></div>
                <span style="font-size: 10px; color: rgba(255,255,255,0.35); letter-spacing: 3px; text-transform: uppercase;">IAP GESTION</span>
            </div>
        </div>

        <div style="position: absolute; bottom: 24px; font-size: 10px; color: rgba(255,255,255,0.12); letter-spacing: 2px;">
            © 2026 SONATRACH — IAP
        </div>
    </div>

    <!-- Right Side - Form -->
    <div style="width: 460px; background: #ffffff; display: flex; flex-direction: column; justify-content: center; padding: 60px 48px; animation: fadeInRight 0.9s ease forwards;">

        <div style="margin-bottom: 36px;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 20px; padding: 6px 14px; margin-bottom: 20px;">
                <div style="width: 6px; height: 6px; background: #f97316; border-radius: 50%; box-shadow: 0 0 6px rgba(249,115,22,0.5);"></div>
                <span style="font-size: 10px; font-weight: 700; color: #ea580c; text-transform: uppercase; letter-spacing: 1.5px;">Réinitialisation</span>
            </div>
            <h2 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">Mot de passe oublié ?</h2>
            <p style="font-size: 13px; color: #94a3b8; margin: 0; line-height: 1.7;">
                Saisissez votre adresse email professionnelle.<br>
                Nous vous enverrons un lien de réinitialisation.
            </p>
        </div>

        @if (session('status'))
            <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px;">
                <svg style="width: 20px; height: 20px; color: #16a34a; flex-shrink: 0; margin-top: 1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p style="font-size: 13px; color: #15803d; font-weight: 600; margin: 0; line-height: 1.6;">
                    {{ session('status') }}
                </p>
            </div>
        @endif

        <form wire:submit.prevent="sendPasswordResetLink" style="display: flex; flex-direction: column; gap: 20px;">

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label style="font-size: 11px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 1px;">Adresse Email</label>
                <div style="position: relative;">
                    <svg style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 17px; height: 17px; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <input wire:model="email" type="email" required autofocus
                        placeholder="prenom.nom@sonatrach.dz"
                        style="width: 100%; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px; padding: 13px 16px 13px 46px; font-size: 14px; color: #111827; outline: none; box-sizing: border-box; font-family: 'Poppins', sans-serif;">
                </div>
                @error('email')
                    <p style="font-size: 12px; color: #D32F2F; font-weight: 600; margin: 0;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                style="width: 100%; background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 50%, #0f2447 100%); color: white; font-weight: 800; padding: 15px 24px; border-radius: 12px; border: none; cursor: pointer; font-size: 13px; text-transform: uppercase; letter-spacing: 2px; box-shadow: 0 8px 24px rgba(10,22,40,0.4); margin-top: 8px; font-family: 'Poppins', sans-serif; position: relative; overflow: hidden;">
                <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(249,115,22,0.1) 0%, transparent 50%);"></div>
                <span style="position: relative;">ENVOYER LE LIEN →</span>
            </button>

        </form>

        <div style="margin-top: 24px; text-align: center;">
            <a href="{{ route('login') }}" style="font-size: 13px; color: #f97316; font-weight: 700; text-decoration: none;">
                ← Retour à la connexion
            </a>
        </div>

        <div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #f3f4f6; text-align: center;">
            <p style="font-size: 11px; color: #d1d5db; margin: 0;">© 2026 SONATRACH — IAP</p>
        </div>

    </div>
</div>

<div x-data="{ resetOpen: false }" @keydown.escape.window="resetOpen = false">

<style>
@keyframes pulse-ring {
    0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.6; }
    100% { transform: translate(-50%, -50%) scale(1.4); opacity: 0; }
}
@keyframes fadeInLeft {
    from { opacity: 0; transform: translateX(-40px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes fadeInRight {
    from { opacity: 0; transform: translateX(40px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes rotate-slow {
    from { transform: translate(-50%, -50%) rotate(0deg); }
    to { transform: translate(-50%, -50%) rotate(360deg); }
}
@keyframes twinkle {
    0%, 100% { opacity: 0.2; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.3); }
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)    scale(1); }
}

/* Hide elements with x-cloak before Alpine initialises */
[x-cloak] { display: none !important; }

/* Centering for the modal overlay — no !important so Alpine's x-show can hide it */
.reset-overlay {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<!-- ── Main layout ── -->
<div style="min-height: 100vh; display: flex; font-family: 'Poppins', sans-serif; overflow: hidden;">

    <!-- Left Side -->
    <div style="flex: 1; background: linear-gradient(145deg, #060d1a 0%, #0a1628 35%, #0d2040 70%, #0a1a35 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 60px; position: relative; overflow: hidden;">

        <div style="position: absolute; top: 50%; left: 50%; width: 700px; height: 700px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.04); animation: rotate-slow 30s linear infinite;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 550px; height: 550px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.06); animation: rotate-slow 20s linear infinite reverse;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 400px; height: 400px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.08);"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 280px; height: 280px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.2); animation: pulse-ring 3s ease-out infinite;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 280px; height: 280px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.15); animation: pulse-ring 3s ease-out infinite 1s;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 280px; height: 280px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.1); animation: pulse-ring 3s ease-out infinite 2s;"></div>
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px; height: 300px; background: radial-gradient(circle, rgba(249,115,22,0.06) 0%, transparent 70%); border-radius: 50%;"></div>

        <div style="position: absolute; top: 18%; left: 12%; width: 3px; height: 3px; background: #f97316; border-radius: 50%; animation: twinkle 2.5s ease-in-out infinite;"></div>
        <div style="position: absolute; top: 25%; left: 80%; width: 4px; height: 4px; background: #f97316; border-radius: 50%; animation: twinkle 3s ease-in-out infinite 0.5s;"></div>
        <div style="position: absolute; top: 72%; left: 10%; width: 3px; height: 3px; background: #60a5fa; border-radius: 50%; animation: twinkle 2s ease-in-out infinite 1s;"></div>
        <div style="position: absolute; top: 80%; left: 75%; width: 5px; height: 5px; background: #f97316; border-radius: 50%; animation: twinkle 3.5s ease-in-out infinite 0.3s;"></div>
        <div style="position: absolute; top: 45%; left: 8%; width: 2px; height: 2px; background: #93c5fd; border-radius: 50%; animation: twinkle 2.8s ease-in-out infinite 1.5s;"></div>
        <div style="position: absolute; top: 60%; left: 85%; width: 3px; height: 3px; background: #f97316; border-radius: 50%; animation: twinkle 2.2s ease-in-out infinite 0.8s;"></div>
        <div style="position: absolute; top: 10%; left: 50%; width: 2px; height: 2px; background: #f97316; border-radius: 50%; animation: twinkle 3.2s ease-in-out infinite 0.2s;"></div>
        <div style="position: absolute; top: 88%; left: 40%; width: 4px; height: 4px; background: #93c5fd; border-radius: 50%; animation: twinkle 2.6s ease-in-out infinite 1.2s;"></div>

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
            <h2 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">Authentification</h2>
            <p style="font-size: 13px; color: #94a3b8; margin: 0; line-height: 1.7;">Veuillez saisir vos identifiants professionnels<br>pour accéder à votre espace de travail.</p>
        </div>

        @if($throttleSeconds > 0)
            <div x-data="{ t: {{ $throttleSeconds }} }"
                 x-init="let iv = setInterval(() => { if (t > 0) t--; else clearInterval(iv); }, 1000)">
                <p style="font-size: 0.85rem; color: #D32F2F; font-weight: 600; margin-bottom: 20px;">
                    Trop de tentatives. Réessayez dans <span x-text="t"></span> seconde<template x-if="t !== 1"><span>s</span></template>.
                </p>
            </div>
        @elseif($authError)
            <p style="font-size: 0.85rem; color: #D32F2F; font-weight: 600; margin-bottom: 20px;">{{ $authError }}</p>
        @endif

        <form wire:submit.prevent="login" style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Email -->
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label style="font-size: 11px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 1px;">Adresse Email</label>
                <div style="position: relative;">
                    <svg style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 17px; height: 17px; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <input wire:model="form.email" type="email" required autofocus
                        placeholder="prenom.nom@sonatrach.dz"
                        style="width: 100%; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px; padding: 13px 16px 13px 46px; font-size: 14px; color: #111827; outline: none; box-sizing: border-box; font-family: 'Poppins', sans-serif;">
                </div>
            </div>

            <!-- Mot de passe -->
            <div style="display: flex; flex-direction: column; gap: 8px;" x-data="{ show: false }">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label style="font-size: 11px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 1px;">Mot de passe</label>
                    <button type="button" @click="resetOpen = true"
                        style="font-size: 12px; color: #f97316; font-weight: 700; background: none; border: none; cursor: pointer; padding: 0; font-family: 'Poppins', sans-serif;">
                        Mot de passe oublié ?
                    </button>
                </div>
                <div style="position: relative;">
                    <svg style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 17px; height: 17px; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <input wire:model="form.password" :type="show ? 'text' : 'password'" required
                        placeholder="••••••••••••"
                        style="width: 100%; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px; padding: 13px 46px 13px 46px; font-size: 14px; color: #111827; outline: none; box-sizing: border-box; font-family: 'Poppins', sans-serif;">
                    <button type="button" @click="show = !show"
                        style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0;">
                        <svg x-show="!show" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"/></svg>
                        <svg x-show="show" x-cloak style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.888 9.888L3.123 3.123m17.754 17.754L14.117 14.117" stroke-width="2"/></svg>
                    </button>
                </div>
            </div>

            <!-- Bouton connexion -->
            <button type="submit"
                style="width: 100%; background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 50%, #0f2447 100%); color: white; font-weight: 800; padding: 15px 24px; border-radius: 12px; border: none; cursor: pointer; font-size: 13px; text-transform: uppercase; letter-spacing: 2px; box-shadow: 0 8px 24px rgba(10,22,40,0.4); margin-top: 8px; font-family: 'Poppins', sans-serif; position: relative; overflow: hidden;">
                <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(249,115,22,0.1) 0%, transparent 50%);"></div>
                <span style="position: relative;">SE CONNECTER →</span>
            </button>

        </form>

        <div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #f3f4f6; text-align: center;">
            <p style="font-size: 11px; color: #d1d5db; margin: 0;">© 2026 SONATRACH — IAP</p>
        </div>

    </div>
</div>
<!-- ── fin main layout ── -->

<!-- ══════════════════════════════════════════════════
     Mini-modale : Mot de passe oublié
     Placée en dehors du overflow:hidden pour que
     position:fixed couvre bien toute la fenêtre.
══════════════════════════════════════════════════ -->
<div x-show="resetOpen"
     x-cloak
     @click.self="resetOpen = false"
     class="reset-overlay"
     style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(10,22,40,0.6);
            z-index: 9999;
            backdrop-filter: blur(4px);">

    <div style="background: #ffffff; border-radius: 18px; padding: 36px 40px;
                width: 100%; max-width: 400px; margin: 0 20px;
                box-shadow: 0 32px 80px rgba(0,0,0,0.3);
                position: relative;
                animation: fadeUp 0.22s ease;">

        <!-- × Fermer -->
        <button type="button" @click="resetOpen = false"
            style="position: absolute; top: 14px; right: 14px;
                   background: #f1f5f9; border: none; border-radius: 8px;
                   width: 32px; height: 32px; cursor: pointer;
                   display: flex; align-items: center; justify-content: center; color: #64748b;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Titre -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; font-family: 'Poppins', sans-serif;">
                Mot de passe oublié ?
            </h3>
            <p style="font-size: 12px; color: #94a3b8; margin: 0; line-height: 1.6; font-family: 'Poppins', sans-serif;">
                Saisissez votre email professionnel — nous vous enverrons un lien de réinitialisation.
            </p>
        </div>

        <!-- Message succès -->
        @if($resetStatus)
            <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px;
                        padding: 12px 16px; margin-bottom: 16px;
                        display: flex; align-items: flex-start; gap: 10px;">
                <svg style="width: 18px; height: 18px; color: #16a34a; flex-shrink: 0; margin-top: 1px;"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p style="font-size: 12px; color: #15803d; font-weight: 600; margin: 0; font-family: 'Poppins', sans-serif;">
                    {{ $resetStatus }}
                </p>
            </div>
        @endif

        <!-- Message erreur -->
        @if($resetError)
            <p style="font-size: 12px; color: #dc2626; font-weight: 600;
                      margin: 0 0 12px 0; font-family: 'Poppins', sans-serif;">
                {{ $resetError }}
            </p>
        @endif

        <!-- Formulaire -->
        <form wire:submit.prevent="sendResetLink" style="display: flex; flex-direction: column; gap: 14px;">

            <div style="position: relative;">
                <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
                             width: 16px; height: 16px; color: #9ca3af;"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input wire:model="resetEmail" type="email"
                    placeholder="prenom.nom@sonatrach.dz"
                    style="width: 100%; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px;
                           padding: 12px 14px 12px 42px; font-size: 13px; color: #0f172a;
                           outline: none; box-sizing: border-box; font-family: 'Poppins', sans-serif;">
            </div>

            @error('resetEmail')
                <p style="font-size: 12px; color: #dc2626; font-weight: 600;
                           margin: -6px 0 0 0; font-family: 'Poppins', sans-serif;">
                    {{ $message }}
                </p>
            @enderror

            <button type="submit"
                style="width: 100%;
                       background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 100%);
                       color: white; font-weight: 700; padding: 13px;
                       border-radius: 10px; border: none; cursor: pointer;
                       font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px;
                       font-family: 'Poppins', sans-serif;
                       box-shadow: 0 6px 20px rgba(10,22,40,0.3);">
                Envoyer le lien →
            </button>

        </form>
    </div>
</div>

</div>

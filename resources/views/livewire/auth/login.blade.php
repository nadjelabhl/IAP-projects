<div style="min-height: 100vh; display: flex; font-family: 'Poppins', sans-serif; overflow: hidden;">

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-15px) rotate(1deg); }
    66% { transform: translateY(-8px) rotate(-1deg); }
}
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
</style>

    <!-- Left Side -->
    <div style="flex: 1; background: linear-gradient(145deg, #060d1a 0%, #0a1628 35%, #0d2040 70%, #0a1a35 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 60px; position: relative; overflow: hidden;">
        
        <!-- Animated rings -->
        <div style="position: absolute; top: 50%; left: 50%; width: 700px; height: 700px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.04); animation: rotate-slow 30s linear infinite;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 550px; height: 550px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.06); animation: rotate-slow 20s linear infinite reverse;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 400px; height: 400px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.08);"></div>

        <!-- Pulse rings -->
        <div style="position: absolute; top: 50%; left: 50%; width: 280px; height: 280px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.2); animation: pulse-ring 3s ease-out infinite;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 280px; height: 280px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.15); animation: pulse-ring 3s ease-out infinite 1s;"></div>
        <div style="position: absolute; top: 50%; left: 50%; width: 280px; height: 280px; border-radius: 50%; border: 1px solid rgba(249,115,22,0.1); animation: pulse-ring 3s ease-out infinite 2s;"></div>

        <!-- Radial glow -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px; height: 300px; background: radial-gradient(circle, rgba(249,115,22,0.06) 0%, transparent 70%); border-radius: 50%;"></div>

        <!-- Floating particles -->
        <div style="position: absolute; top: 18%; left: 12%; width: 3px; height: 3px; background: #f97316; border-radius: 50%; animation: twinkle 2.5s ease-in-out infinite;"></div>
        <div style="position: absolute; top: 25%; left: 80%; width: 4px; height: 4px; background: #f97316; border-radius: 50%; animation: twinkle 3s ease-in-out infinite 0.5s;"></div>
        <div style="position: absolute; top: 72%; left: 10%; width: 3px; height: 3px; background: #60a5fa; border-radius: 50%; animation: twinkle 2s ease-in-out infinite 1s;"></div>
        <div style="position: absolute; top: 80%; left: 75%; width: 5px; height: 5px; background: #f97316; border-radius: 50%; animation: twinkle 3.5s ease-in-out infinite 0.3s;"></div>
        <div style="position: absolute; top: 45%; left: 8%; width: 2px; height: 2px; background: #93c5fd; border-radius: 50%; animation: twinkle 2.8s ease-in-out infinite 1.5s;"></div>
        <div style="position: absolute; top: 60%; left: 85%; width: 3px; height: 3px; background: #f97316; border-radius: 50%; animation: twinkle 2.2s ease-in-out infinite 0.8s;"></div>
        <div style="position: absolute; top: 10%; left: 50%; width: 2px; height: 2px; background: #f97316; border-radius: 50%; animation: twinkle 3.2s ease-in-out infinite 0.2s;"></div>
        <div style="position: absolute; top: 88%; left: 40%; width: 4px; height: 4px; background: #93c5fd; border-radius: 50%; animation: twinkle 2.6s ease-in-out infinite 1.2s;"></div>

        <!-- Content -->
        <div style="position: relative; z-index: 1; text-align: center; max-width: 400px; animation: fadeInLeft 0.9s ease forwards;">
            
            <!-- Logo fixe orange sans animation -->
            <div style="position: relative; display: inline-block; margin-bottom: 36px;">
                <div style="position: absolute; inset: -25px; background: radial-gradient(circle, rgba(249,115,22,0.08) 0%, transparent 70%); border-radius: 50%;"></div>
                <img src="{{ asset('images/sonatrach-logo.png.png') }}" alt="IAP"
                    style="width: 130px; height: 130px; object-fit: contain; position: relative; z-index: 1; filter: brightness(0) saturate(100%) invert(55%) sepia(90%) saturate(800%) hue-rotate(340deg) brightness(105%);">
            </div>
            
            <!-- Title blanc + bleu clair -->
            <h1 style="font-size: 38px; font-weight: 900; margin: 0; line-height: 1.2; letter-spacing: -1px;">
                <span style="color: white;">INSTITUT ALGÉRIEN DU PÉTROLE</span><br>
                
            </h1>
            
            <!-- Divider -->
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin: 20px auto;">
                <div style="width: 30px; height: 1px; background: linear-gradient(90deg, transparent, #f97316);"></div>
                <div style="width: 6px; height: 6px; background: #f97316; border-radius: 50%;"></div>
                <div style="width: 30px; height: 1px; background: linear-gradient(90deg, #f97316, transparent);"></div>
            </div>

            <p style="font-size: 12px; color: rgba(255,255,255,0.4); margin: 0; letter-spacing: 2px; text-transform: uppercase;">
                Plateforme de Gestion des Projets
            </p>

            <!-- Badge -->
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
                <span style="font-size: 10px; font-weight: 700; color: #ea580c; text-transform: uppercase; letter-spacing: 1.5px;">Accès Sécurisé</span>
            </div>
            <h2 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">Authentification</h2>
            <p style="font-size: 13px; color: #94a3b8; margin: 0; line-height: 1.7;">Veuillez saisir vos identifiants professionnels<br>pour accéder à votre espace de travail.</p>
        </div>

        @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                <svg style="width: 18px; height: 18px; color: #ef4444; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span style="font-size: 13px; color: #dc2626; font-weight: 600;">{{ $errors->first() }}</span>
            </div>
        @endif

        <form wire:submit.prevent="login" style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Email -->
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label style="font-size: 11px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 1px;">Adresse Email</label>
                <div style="position: relative;">
                    <svg style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 17px; height: 17px; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <input wire:model="form.email" type="email" required autofocus
                        placeholder="prenom.nom@iap.sonatrach.dz"
                        style="width: 100%; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px; padding: 13px 16px 13px 46px; font-size: 14px; color: #111827; outline: none; box-sizing: border-box; font-family: 'Poppins', sans-serif;">
                </div>
            </div>

            <!-- Mot de passe -->
            <div style="display: flex; flex-direction: column; gap: 8px;" x-data="{ show: false }">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label style="font-size: 11px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 1px;">Mot de passe</label>
                    <a href="#" style="font-size: 12px; color: #f97316; font-weight: 700; text-decoration: none;">Mot de passe oublié ?</a>
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

            <!-- Bouton -->
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
<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $photo = null;

    public function mount(): void
    {
        $this->name  = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255',
                        Rule::unique(User::class)->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->photo) {
            $path = $this->photo->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        $user->fill([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <form wire:submit="updateProfileInformation">

        {{-- Photo de profil --}}
        <div style="display:flex; align-items:center; gap:20px; margin-bottom:28px;">
            <div style="position:relative;">
                @if($photo)
                    <img src="{{ $photo->temporaryUrl() }}"
                        style="width:88px; height:88px; border-radius:50%; object-fit:cover; border:3px solid #1565C0;">
                @elseif(Auth::user()->profile_photo)
                    <img src="{{ Storage::url(Auth::user()->profile_photo) }}"
                        style="width:88px; height:88px; border-radius:50%; object-fit:cover; border:3px solid #1565C0;">
                @else
                    <div style="width:88px; height:88px; border-radius:50%; background:linear-gradient(135deg,#1565C0,#F37021); display:flex; align-items:center; justify-content:center;">
                        <span style="font-size:32px; font-weight:900; color:white;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
                <label for="photo-upload"
                    style="position:absolute; bottom:0; right:0; background:#1565C0; border-radius:50%; width:26px; height:26px; display:flex; align-items:center; justify-content:center; cursor:pointer; border:2px solid white;">
                    <svg style="width:13px; height:13px; color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </label>
                <input id="photo-upload" type="file" wire:model="photo" accept="image/*" style="display:none;">
            </div>
            <div>
                <p style="font-size:15px; font-weight:800; color:#0f172a; margin:0;">{{ Auth::user()->name }}</p><p style="font-size:12px; color:#64748b; margin:4px 0 0 0;">Cliquez sur l'icône pour changer la photo</p>
                @error('photo')
                    <p style="font-size:11px; color:#ef4444; margin:4px 0 0 0;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Nom --}}
        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">
                Nom complet
            </label>
            <input wire:model="name" type="text" required autofocus autocomplete="name"
                style="width:100%; border:1.5px solid #e2e8f0; border-radius:10px; padding:10px 14px; font-size:14px; color:#0f172a; outline:none; box-sizing:border-box;"
                onfocus="this.style.borderColor='#1565C0'" onblur="this.style.borderColor='#e2e8f0'">
            @error('name')
                <p style="font-size:11px; color:#ef4444; margin:4px 0 0 0;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div style="margin-bottom:20px;">
            <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">
                Adresse email
            </label>
            <input wire:model="email" type="email" required autocomplete="username"
                style="width:100%; border:1.5px solid #e2e8f0; border-radius:10px; padding:10px 14px; font-size:14px; color:#0f172a; outline:none; box-sizing:border-box;"
                onfocus="this.style.borderColor='#1565C0'" onblur="this.style.borderColor='#e2e8f0'">
            @error('email')
                <p style="font-size:11px; color:#ef4444; margin:4px 0 0 0;">{{ $message }}</p>
            @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <p style="font-size:12px; color:#f97316; margin:8px 0 0 0;">
                    Email non vérifié.
                    <button wire:click.prevent="sendVerification"
                            style="color:#1565C0; text-decoration:underline; background:none; border:none; cursor:pointer; font-size:12px;">
                        Renvoyer la vérification
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p style="font-size:12px; color:#22c55e; margin:4px 0 0 0;">Lien envoyé !</p>
                @endif
            @endif
        </div>

        {{-- Bouton --}}
        <div style="display:flex; align-items:center; gap:12px;">
            <button type="submit"
                    style="background:#1565C0; color:white; font-size:13px; font-weight:700; padding:10px 24px; border-radius:10px; border:none; cursor:pointer;">
                Enregistrer
            </button>
            <x-action-message on="profile-updated">
                <span style="font-size:12px; color:#22c55e; font-weight:600;">✓ Sauvegardé</span>
            </x-action-message>
        </div>

    </form>
</section>
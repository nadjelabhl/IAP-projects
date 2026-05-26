<div class="p-8 max-w-lg space-y-6">

    {{-- Header --}}
    <div>
        <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">Mon Profil</h1>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-800 text-sm font-semibold">
        {{ session('success') }}
    </div>
    @endif
    @if(session('successPwd'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-800 text-sm font-semibold">
        {{ session('successPwd') }}
    </div>
    @endif

    {{-- Identity card --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-center gap-4 px-6 py-5 border-b border-slate-100">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center shrink-0">
                <span class="text-xl font-black text-white leading-none">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            </div>
            <div class="min-w-0">
                <p class="font-black text-slate-900 text-base leading-tight truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 mt-0.5 truncate">{{ auth()->user()->email }}</p>
            </div>
            <span class="ml-auto shrink-0 text-[10px] font-bold text-orange-600 bg-orange-50 border border-orange-200 px-2.5 py-1 rounded-full uppercase tracking-wide">
                {{ auth()->user()->role_label }}
            </span>
        </div>

        {{-- Modifier le nom --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Modifier le nom</p>
            <div class="flex gap-3">
                <input wire:model="name" type="text" placeholder="Nom complet"
                    class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                <button wire:click="saveProfile"
                    class="shrink-0 bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-colors">
                    Enregistrer
                </button>
            </div>
            @error('name') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Changer le mot de passe --}}
        <div class="px-6 py-5">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Changer le mot de passe</p>
            <div class="space-y-3">
                <div>
                    <input wire:model="currentPassword" type="password" placeholder="Mot de passe actuel"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    @error('currentPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <input wire:model="newPassword" type="password" placeholder="Nouveau mot de passe"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    @error('newPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <input wire:model="newPassword_confirmation" type="password" placeholder="Confirmer le nouveau mot de passe"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                </div>
                <button wire:click="changePassword"
                    class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm py-2.5 rounded-xl transition-colors">
                    Mettre à jour le mot de passe
                </button>
            </div>
        </div>
    </div>

</div>

@php $inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-400/40 focus:border-orange-400 transition'; @endphp

<div>
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Nom complet <span class="text-red-400">*</span></label>
    <input type="text" wire:model="uName" placeholder="Ex. Ali Mebarki" class="{{ $inputClass }}">
    @error('uName') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Adresse e-mail <span class="text-red-400">*</span></label>
    <input type="email" wire:model="uEmail" placeholder="utilisateur@sonatrach.dz" class="{{ $inputClass }}">
    @error('uEmail') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-xs font-bold text-slate-600 mb-1.5">
        Mot de passe {{ $editingId ? '(laisser vide pour conserver)' : '' }}
        @if(!$editingId) <span class="text-red-400">*</span> @endif
    </label>
    <input type="password" wire:model="uPassword" placeholder="{{ $editingId ? '••••••••' : 'Min. 6 caractères' }}" class="{{ $inputClass }}">
    @error('uPassword') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Rôle <span class="text-red-400">*</span></label>
    <select wire:model="uRole" class="{{ $inputClass }}">
        <option value="admin">Administrateur</option>
        <option value="dg">Direction Générale</option>
        <option value="assistant_dg">Assistante DG</option>
        <option value="directeur_ecole">Directeur d'École</option>
        <option value="juriste">Juriste</option>
        <option value="chef_projet">Chef de Projet</option>
    </select>
    @error('uRole') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-xs font-bold text-slate-600 mb-1.5">École (optionnel)</label>
    <select wire:model="uSchoolId" class="{{ $inputClass }}">
        <option value="">— Aucune école —</option>
        @foreach($schools as $school)
            <option value="{{ $school->id }}">{{ $school->name }}</option>
        @endforeach
    </select>
</div>

<div class="flex items-center justify-between py-1">
    <p class="text-xs font-bold text-slate-600">Compte actif</p>
    <button type="button" wire:click="$toggle('uIsActive')"
        class="relative inline-flex h-6 w-11 rounded-full transition-colors {{ $uIsActive ? 'bg-orange-500' : 'bg-slate-200' }}">
        <span class="sr-only">Activer</span>
        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition-transform mt-0.5 {{ $uIsActive ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
    </button>
</div>

<div class="space-y-6">
    {{-- ====================================================================
         EN-TÊTE DU PROJET
    ===================================================================== --}}
    <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-iap-orange">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900">{{ $project->title }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $project->school->name }} &bull; {{ $project->nature->name }} &bull; {{ $project->type }}
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold
                @if($project->status === 'En Cours') bg-green-100 text-green-700
                @elseif($project->status === 'En Etude') bg-blue-100 text-blue-700
                @else bg-slate-100 text-slate-600 @endif">
                {{ $project->status }}
            </span>
        </div>
    </div>

    {{-- Messages flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-green-800 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-800 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- ====================================================================
         PROGRESSION GLOBALE
    ===================================================================== --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-slate-700">Avancement juridique</h2>
            <span class="text-2xl font-black
                @if($completedPct >= 100) text-green-600
                @elseif($completedPct >= 50) text-iap-orange
                @else text-slate-500 @endif">
                {{ number_format($completedPct, 1) }} %
            </span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-4">
            <div class="h-4 rounded-full transition-all duration-500
                @if($completedPct >= 100) bg-green-500
                @elseif($completedPct >= 80) bg-yellow-500
                @else bg-iap-orange @endif"
                style="width: {{ min($completedPct, 100) }}%">
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-2">
            Total défini : {{ number_format($totalPct, 1) }} % / 100 %
            @if($totalPct > 100)
                <span class="text-red-600 font-bold">&mdash; DÉPASSEMENT !</span>
            @endif
        </p>
    </div>

    {{-- ====================================================================
         LISTE DES PHASES
    ===================================================================== --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Référentiel juridique</h2>
            <span class="text-xs text-slate-400">Double-cliquer sur une phase pour la modifier</span>
        </div>

        @forelse($steps as $step)
            <div class="flex items-center gap-4 px-6 py-4 border-b border-slate-50 hover:bg-slate-50 transition
                @if($step->is_completed) bg-green-50 @endif"
                x-data="{ hover: false }" @mouseenter="hover=true" @mouseleave="hover=false">

                {{-- Checkbox --}}
                <button wire:click="toggleStep({{ $step->id }})"
                    class="flex-shrink-0 w-6 h-6 rounded border-2 flex items-center justify-center transition
                        @if($step->is_completed) border-green-500 bg-green-500 text-white
                        @else border-slate-300 bg-white hover:border-iap-orange @endif">
                    @if($step->is_completed)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    @endif
                </button>

                {{-- Numéro --}}
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">
                    {{ $step->sort_order }}
                </span>

                {{-- Titre + infos --}}
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800 {{ $step->is_completed ? 'line-through text-slate-400' : '' }} truncate">
                        {{ $step->title }}
                    </p>
                    @if($step->is_completed && $step->checked_at)
                        <p class="text-xs text-green-600">Cochée le {{ $step->checked_at->format('d/m/Y') }}</p>
                    @endif
                </div>

                {{-- Pourcentage --}}
                <span class="flex-shrink-0 font-bold text-sm
                    @if($step->is_completed) text-green-600 @else text-iap-orange @endif">
                    {{ number_format($step->percentage, 1) }} %
                </span>

                {{-- PDF --}}
                @if($step->pdf_path)
                    <a href="{{ Storage::url($step->pdf_path) }}" target="_blank"
                        class="flex-shrink-0 text-blue-500 hover:text-blue-700" title="Voir le PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </a>
                @else
                    <button wire:click="startUpload({{ $step->id }})"
                        class="flex-shrink-0 text-slate-400 hover:text-iap-orange" title="Uploader PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </button>
                @endif

                {{-- Bouton édition --}}
                @if(!$step->is_completed)
                    <button wire:click="openEditModal({{ $step->id }})"
                        class="flex-shrink-0 text-slate-400 hover:text-iap-orange" title="Modifier">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                @endif

                {{-- Bouton suppression --}}
                @if($step->is_deletable && !$step->is_completed)
                    <button wire:click="deleteStep({{ $step->id }})"
                        wire:confirm="Supprimer cette phase ?"
                        class="flex-shrink-0 text-slate-400 hover:text-red-500" title="Supprimer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                @endif
            </div>
        @empty
            <div class="px-6 py-10 text-center text-slate-400">Aucune phase définie.</div>
        @endforelse
    </div>

    {{-- ====================================================================
         UPLOAD PDF (zone contextuelle)
    ===================================================================== --}}
    @if($uploadingStepId)
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
            <h3 class="font-bold text-blue-800 mb-3">Uploader le PDF pour cette phase</h3>
            <form wire:submit="uploadPdf" class="flex items-center gap-4">
                <input type="file" wire:model="stepPdf" accept=".pdf"
                    class="flex-1 text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-iap-orange file:text-white file:font-semibold hover:file:bg-orange-600">
                <button type="submit"
                    class="bg-blue-600 text-white font-bold px-6 py-2 rounded-xl hover:bg-blue-700 transition">
                    Enregistrer
                </button>
                <button type="button" wire:click="$set('uploadingStepId', null)"
                    class="text-slate-500 hover:text-slate-700">Annuler</button>
            </form>
            @error('stepPdf') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
        </div>
    @endif

    {{-- ====================================================================
         FORMULAIRE AJOUT DE PHASE
    ===================================================================== --}}
    @if($project->status !== 'En Cours')
        <div class="bg-white rounded-2xl shadow p-6">
            <h3 class="font-bold text-slate-800 mb-4">Ajouter une phase</h3>
            @if($totalPct >= 100)
                <p class="text-amber-600 text-sm font-semibold">
                    Le total atteint déjà 100 %. Impossible d'ajouter une nouvelle phase.
                </p>
            @else
                <form wire:submit="addStep" class="flex gap-3 flex-wrap">
                    <div class="flex-1 min-w-48">
                        <input wire:model="newTitle" type="text" placeholder="Nom de la phase"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 outline-none">
                        @error('newTitle') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="w-36">
                        <input wire:model="newPercentage" type="number" step="0.01" min="0.01"
                            max="{{ 100 - $totalPct }}" placeholder="% (max {{ number_format(100 - $totalPct, 1) }})"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 outline-none">
                        @error('newPercentage') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                        class="bg-iap-orange text-white font-bold px-6 py-2 rounded-xl hover:bg-orange-600 transition">
                        + Ajouter
                    </button>
                </form>
            @endif
        </div>
    @endif

    {{-- ====================================================================
         BOUTONS ODS
    ===================================================================== --}}
    <div class="flex flex-wrap gap-3">
        @if($canEmitDemarrage && $project->status === 'En Etude')
            <button wire:click="$set('odsDemarrageModalOpen', true)"
                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-xl shadow transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Émettre l'ODS de Démarrage
            </button>
        @endif

        @if($project->status === 'En Cours')
            <button wire:click="$set('odsArretModalOpen', true)"
                class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl shadow transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                </svg>
                ODS d'Arrêt
            </button>
            <button wire:click="$set('odsRepriseModalOpen', true)"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl shadow transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                ODS de Reprise
            </button>
        @endif
    </div>

    {{-- ====================================================================
         HISTORIQUE ODS
    ===================================================================== --}}
    @if($odsHistory->isNotEmpty())
        <div class="bg-white rounded-2xl shadow p-6">
            <h3 class="font-bold text-slate-800 mb-4">Historique des ODS</h3>
            <div class="space-y-3">
                @foreach($odsHistory as $ods)
                    <div class="flex items-center gap-4 p-3 rounded-xl
                        @if($ods->type === 'Demarrage') bg-green-50 border border-green-200
                        @elseif($ods->type === 'Arret') bg-red-50 border border-red-200
                        @else bg-blue-50 border border-blue-200 @endif">
                        <span class="font-bold text-sm
                            @if($ods->type === 'Demarrage') text-green-700
                            @elseif($ods->type === 'Arret') text-red-700
                            @else text-blue-700 @endif">
                            ODS {{ $ods->type }}
                        </span>
                        <span class="text-xs text-slate-500">
                            par {{ $ods->issuedBy->name }} &bull; {{ $ods->issued_at->format('d/m/Y H:i') }}
                        </span>
                        @if($ods->notes)
                            <span class="text-xs text-slate-500 flex-1 truncate italic">{{ $ods->notes }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ====================================================================
         MODALE ÉDITION
    ===================================================================== --}}
    @if($editModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 mx-4">
                <h3 class="font-bold text-slate-800 text-lg mb-4">Modifier la phase</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Numéro d'ordre</label>
                        <input wire:model="editOrder" type="number" min="1"
                            class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-iap-orange outline-none">
                        @error('editOrder') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nom de la phase</label>
                        <input wire:model="editTitle" type="text"
                            class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-iap-orange outline-none">
                        @error('editTitle') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pourcentage (%)</label>
                        <input wire:model="editPercentage" type="number" step="0.01" min="0.01"
                            class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-iap-orange outline-none">
                        @error('editPercentage') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="saveEdit"
                        class="flex-1 bg-iap-orange text-white font-bold py-2 rounded-xl hover:bg-orange-600 transition">
                        Enregistrer
                    </button>
                    <button wire:click="closeEditModal"
                        class="flex-1 bg-slate-100 text-slate-700 font-bold py-2 rounded-xl hover:bg-slate-200 transition">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ====================================================================
         MODALE ODS DÉMARRAGE
    ===================================================================== --}}
    @if($odsDemarrageModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 mx-4">
                <h3 class="font-bold text-green-700 text-lg mb-2">Émettre l'ODS de Démarrage</h3>
                <p class="text-sm text-slate-600 mb-4">
                    Cette action va changer le statut du projet à <strong>En Cours</strong> et débloquer l'accès au Chef de Projet.
                </p>
                <textarea wire:model="odesDemarrageNotes" rows="3" placeholder="Notes (facultatif)"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none mb-4"></textarea>
                <div class="flex gap-3">
                    <button wire:click="emitOdsDemarrage"
                        class="flex-1 bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition">
                        Confirmer l'émission
                    </button>
                    <button wire:click="$set('odsDemarrageModalOpen', false)"
                        class="flex-1 bg-slate-100 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-200 transition">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODALE ODS ARRÊT --}}
    @if($odsArretModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 mx-4">
                <h3 class="font-bold text-red-700 text-lg mb-2">ODS d'Arrêt</h3>
                <p class="text-sm text-slate-600 mb-4">Décrivez l'incident motivant l'arrêt des travaux.</p>
                <textarea wire:model="odsArretNotes" rows="4" placeholder="Description de l'incident (obligatoire, min. 10 caractères)"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:border-red-500 outline-none mb-4"></textarea>
                @error('odsArretNotes') <p class="text-red-600 text-xs mb-2">{{ $message }}</p> @enderror
                <div class="flex gap-3">
                    <button wire:click="emitOdsArret"
                        class="flex-1 bg-red-600 text-white font-bold py-3 rounded-xl hover:bg-red-700 transition">
                        Émettre l'ODS d'Arrêt
                    </button>
                    <button wire:click="$set('odsArretModalOpen', false)"
                        class="flex-1 bg-slate-100 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-200 transition">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODALE ODS REPRISE --}}
    @if($odsRepriseModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 mx-4">
                <h3 class="font-bold text-blue-700 text-lg mb-2">ODS de Reprise</h3>
                <p class="text-sm text-slate-600 mb-4">Confirmez la résolution de l'incident et la reprise des travaux.</p>
                <textarea wire:model="odsRepriseNotes" rows="4" placeholder="Description de la reprise (obligatoire, min. 10 caractères)"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none mb-4"></textarea>
                @error('odsRepriseNotes') <p class="text-red-600 text-xs mb-2">{{ $message }}</p> @enderror
                <div class="flex gap-3">
                    <button wire:click="emitOdsReprise"
                        class="flex-1 bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition">
                        Émettre l'ODS de Reprise
                    </button>
                    <button wire:click="$set('odsRepriseModalOpen', false)"
                        class="flex-1 bg-slate-100 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-200 transition">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

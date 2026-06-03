<div class="p-8 space-y-6">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-3.5 text-sm font-semibold">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">Bienvenue, {{ auth()->user()->name }}</h1>
        </div>
        {{-- Bell icon top-right → notifications --}}
        <a href="{{ route('notifications.index') }}" class="relative shrink-0 mt-1 w-11 h-11 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-500 hover:text-orange-500 hover:border-orange-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] font-black flex items-center justify-center">{{ min($unreadCount, 9) }}</span>
            @endif
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-5 gap-4">
        @foreach([
            ['TOTAL PROJETS', $stats['total'],    'slate'],
            ['NOUVEAU',       $stats['nouveau'],  'amber'],
            ['EN ETUDE',      $stats['en_etude'], 'orange'],
            ['EN COURS',      $stats['en_cours'], 'blue'],
            ['TERMINÉ',       $stats['termine'],  'green'],
        ] as [$label, $value, $tone])
        @php
            $toneMap = [
                'slate'  => ['text-slate-900',   'border-slate-200'],
                'amber'  => ['text-amber-500',   'border-amber-200'],
                'orange' => ['text-orange-500',  'border-orange-200'],
                'blue'   => ['text-blue-500',    'border-blue-200'],
                'green'  => ['text-emerald-500', 'border-emerald-200'],
            ];
            [$textColor, $border] = $toneMap[$tone];
        @endphp
        <div class="bg-white rounded-2xl border {{ $border }} p-5 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">{{ $label }}</p>
            <p class="text-5xl font-black {{ $textColor }} tabular-nums leading-none">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    {{-- Notifications (under stats) --}}
    @if($notifications->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h2 class="text-sm font-black text-slate-900">Notifications</h2>
                @if($unreadCount > 0)
                <span class="text-[11px] font-bold bg-orange-100 text-orange-600 px-2.5 py-0.5 rounded-full">{{ $unreadCount }} non lu{{ $unreadCount > 1 ? 'es' : '' }}</span>
                @endif
            </div>
        </div>
        <div class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
            @foreach($notifications as $notif)
            <div class="px-6 py-3 flex items-start gap-3 {{ $notif->is_read ? 'opacity-60' : '' }}"
                 wire:click="markNotificationRead({{ $notif->id }})">
                <div class="mt-0.5 w-2 h-2 rounded-full shrink-0 {{ $notif->is_read ? 'bg-slate-200' : 'bg-orange-400' }}"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $notif->message }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Calendrier des Projets (Gantt) --}}
    @php $colW = 52; $totalW = $numMonths * $colW; @endphp
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
         x-data="{
             drag: false, startX: 0, startScroll: 0,
             init() {
                 this.$nextTick(() => {
                     const idx    = {{ $currentMonthIdx }};
                     const colW   = {{ $colW }};
                     const offset = idx * colW - this.$refs.track.clientWidth / 3;
                     this.$refs.track.scrollLeft = Math.max(0, offset);
                 });
             },
             beginDrag(e) {
                 const cx = e.touches ? e.touches[0].clientX : e.clientX;
                 this.drag = true; this.startX = cx;
                 this.startScroll = this.$refs.track.scrollLeft;
                 this.$refs.track.style.cursor = 'grabbing';
             },
             moveDrag(e) {
                 if (!this.drag) return; e.preventDefault();
                 const cx = e.touches ? e.touches[0].clientX : e.clientX;
                 this.$refs.track.scrollLeft = this.startScroll - (cx - this.startX);
             },
             endDrag() { this.drag = false; this.$refs.track.style.cursor = 'grab'; },
             onWheel(e) {
                 e.preventDefault();
                 this.$refs.track.scrollLeft += e.deltaX !== 0 ? e.deltaX : e.deltaY;
             },
             nudge(dir) { this.$refs.track.scrollBy({ left: dir * {{ $colW * 3 }}, behavior: 'smooth' }); }
         }">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-black text-slate-900">Calendrier des Projets</h2>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-3 text-[10px] font-bold text-slate-400 mr-3">
                    <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-2.5 rounded" style="background:#f59e0b"></span>Nouveau</span>
                    <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-2.5 rounded" style="background:#f97316"></span>En Étude</span>
                    <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-2.5 rounded" style="background:#3b82f6"></span>En Cours</span>
                </div>
                {{-- Arrow buttons --}}
                <button @click="nudge(-1)"
                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button @click="nudge(1)"
                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Scrollable track --}}
        <div x-ref="track"
             @mousedown="beginDrag($event)"
             @mousemove="moveDrag($event)"
             @mouseup="endDrag()"
             @mouseleave="endDrag()"
             @wheel.prevent="onWheel($event)"
             @touchstart.passive="beginDrag($event)"
             @touchmove.prevent="moveDrag($event)"
             @touchend="endDrag()"
             class="overflow-x-auto select-none"
             style="cursor:grab; scrollbar-width:thin;">
            <div style="width:{{ $totalW }}px;">

                {{-- ① Year header --}}
                <div class="flex bg-slate-800">
                    @foreach($yearGroups as $year => $count)
                    <div class="text-center text-xs font-black text-white py-2.5 border-r border-slate-700 last:border-r-0"
                         style="width:{{ $count * $colW }}px; min-width:{{ $count * $colW }}px;">
                        {{ $year }}
                    </div>
                    @endforeach
                </div>

                {{-- ② Month labels --}}
                <div class="flex border-b border-slate-200 bg-slate-50">
                    @foreach($ganttMonths as $m)
                    <div class="shrink-0 text-center border-r border-slate-100 last:border-r-0 py-1.5"
                         style="width:{{ $colW }}px; min-width:{{ $colW }}px; max-width:{{ $colW }}px; overflow:hidden;">
                        <span class="text-[10px] font-black {{ $m['isCurrent'] ? 'text-orange-500' : 'text-slate-400' }}">
                            {{ $m['label'] }}
                        </span>
                    </div>
                    @endforeach
                </div>

                {{-- ③ Project bars --}}
                <div class="relative" style="min-height:{{ max(64, count($ganttProjects) * 44 + 16) }}px;">

                    {{-- Grid columns --}}
                    <div class="absolute inset-0 flex pointer-events-none">
                        @foreach($ganttMonths as $i => $m)
                        <div class="shrink-0 border-r border-slate-100 h-full {{ $m['isCurrent'] ? 'bg-orange-50/40' : ($i % 2 !== 0 ? 'bg-slate-50/30' : '') }}"
                             style="width:{{ $colW }}px; min-width:{{ $colW }}px;"></div>
                        @endforeach
                    </div>

                    {{-- Bars --}}
                    @forelse($ganttProjects as $idx => $gp)
                    @php
                        $barBg = match($gp['status']) {
                            'Nouveau'  => '#f59e0b',
                            'En Etude' => '#f97316',
                            'En Cours' => '#3b82f6',
                            default    => '#6b7280',
                        };
                        $top  = 8 + $idx * 44;
                        $left = $gp['leftMonths'] * $colW + 2;
                        $w    = max(1, $gp['widthMonths']) * $colW - 4;
                    @endphp
                    <div class="absolute flex items-center rounded-xl px-3 shadow-sm"
                         style="top:{{ $top }}px; left:{{ $left }}px; width:{{ $w }}px; height:36px; background-color:{{ $barBg }};">
                        <span class="text-[11px] font-bold text-white truncate">{{ $gp['title'] }}</span>
                    </div>
                    @empty
                    <div class="flex items-center justify-center" style="height:64px;">
                        <p class="text-sm text-slate-400 font-semibold">Aucun projet actif.</p>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    {{-- Nouveaux Projets --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-black text-slate-900">Nouveaux projets</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">PROJET</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">NATURE</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">BUDGET</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">STATUT</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">FICHE</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">AFFECTATION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($newProjects as $project)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="text-sm font-bold text-slate-900">{{ $project->title_project }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $project->type_project }} · durée {{ $project->duration_months }} mois</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600 whitespace-nowrap">
                            {{ $project->nature->name_nature ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-800 tabular-nums whitespace-nowrap">
                            {{ number_format($project->budget, 0, ',', ' ') }} KDA
                        </td>
                        <td class="px-5 py-4">
                            <x-status-badge :status="$project->status"/>
                        </td>
                        <td class="px-5 py-4">
                            <button wire:click="openDetailModal({{ $project->id }})"
                                class="inline-flex items-center gap-1.5 text-sm text-slate-600 hover:text-orange-500 font-semibold transition-colors whitespace-nowrap">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Fiche projet
                            </button>
                        </td>
                        <td class="px-5 py-4">
                            <button wire:click="openAssignModal({{ $project->id }})"
                                class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-4 py-2 rounded-xl transition-colors whitespace-nowrap shadow-sm shadow-orange-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
                                </svg>
                                Affecter le personnel
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-semibold text-slate-400">Aucun nouveau projet à traiter</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================== MODAL AFFECTATION ===================== --}}
    @if($showAssignModal)
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
         style="z-index:99999;"
         wire:click.self="closeAssignModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">Affecter le personnel</h3>
                <button wire:click="closeAssignModal" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-600 mb-1.5 block">Juriste <span class="text-red-400">*</span></label>
                    <select wire:model="selectedJuriste" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400/40 focus:border-orange-400 transition">
                        <option value="">— Sélectionner un juriste —</option>
                        @foreach($juristes as $j)
                        <option value="{{ $j->id }}">{{ $j->name }}</option>
                        @endforeach
                    </select>
                    @if($juristes->isEmpty())<p class="text-amber-600 text-xs mt-1 font-semibold">Aucun juriste disponible.</p>@endif
                    @error('selectedJuriste') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600 mb-1.5 block">Chef de Projet <span class="text-red-400">*</span></label>
                    <select wire:model="selectedChef" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400/40 focus:border-orange-400 transition">
                        <option value="">— Sélectionner un chef de projet —</option>
                        @foreach($chefs as $c)
                        @if((string)$c->id !== (string)$selectedJuriste)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endif
                        @endforeach
                    </select>
                    @if($chefs->isEmpty())<p class="text-amber-600 text-xs mt-1 font-semibold">Aucun chef disponible.</p>@endif
                    @error('selectedChef') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="closeAssignModal" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100">Annuler</button>
                <button wire:click="assignPersonnel" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-sm shadow-orange-200">Confirmer l'affectation</button>
            </div>
        </div>
    </div>
    @endif

    {{-- ===================== MODAL FICHE PROJET ===================== --}}
    @if($showDetailModal && $detailProject)
    @php $dp = $detailProject; @endphp
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
         style="z-index:99999;"
         wire:click.self="closeDetailModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

            {{-- Header --}}
            <div class="sticky top-0 bg-white px-6 py-5 border-b border-slate-100 flex items-start justify-between z-10">
                <div>
                    <p class="text-[10px] font-bold text-orange-500 uppercase tracking-widest mb-0.5">Fiche de projet</p>
                    <h3 class="font-black text-slate-900 text-base leading-tight">{{ $dp->title_project }}</h3>
                </div>
                <button wire:click="closeDetailModal" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-5">

                {{-- Identité --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">École</p>
                        <p class="text-sm font-bold text-slate-900">{{ $dp->school->name_school ?? '—' }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nature</p>
                        <p class="text-sm font-bold text-slate-900">{{ $dp->nature->name_nature ?? '—' }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Type</p>
                        <p class="text-sm font-bold text-slate-900">{{ $dp->type_project }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Statut</p>
                        <x-status-badge :status="$dp->status"/>
                    </div>
                </div>

                {{-- Finances & calendrier --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Budget</p>
                        <p class="text-sm font-black text-slate-900">{{ number_format($dp->budget, 0, ',', ' ') }} KDA</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Durée</p>
                        <p class="text-sm font-bold text-slate-900">{{ $dp->duration_months }} mois</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Période</p>
                        <p class="text-sm font-bold text-slate-900">{{ \Carbon\Carbon::parse($dp->start_date)->format('Y') }} – {{ \Carbon\Carbon::parse($dp->end_date)->format('Y') }}</p>
                    </div>
                </div>

                {{-- Localisation --}}
                @if($dp->localisation)
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Adresse / Lieu du projet</p>
                    <p class="text-sm text-slate-700">{{ $dp->localisation }}</p>
                </div>
                @endif

                {{-- Description --}}
                @if($dp->description)
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Description</p>
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $dp->description }}</p>
                </div>
                @endif

                {{-- Soumis par --}}
                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs text-slate-400">
                    <span>Soumis par : <span class="font-semibold text-slate-600">{{ $dp->creator->name ?? 'Assistante DG' }}</span></span>
                    <span>{{ $dp->created_at->format('d/m/Y à H:i') }}</span>
                </div>


            </div>
        </div>
    </div>
    @endif

</div>

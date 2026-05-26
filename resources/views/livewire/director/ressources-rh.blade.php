<div class="p-8 space-y-6">

    {{-- Header --}}
    <div>
        <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">RESSOURCES HUMAINES</h1>
    </div>

    {{-- Juristes --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-900">
            <h2 class="text-sm font-black text-white uppercase tracking-wide">Juristes</h2>
            <span class="text-xs text-slate-400 font-semibold">{{ $juristes->count() }} juriste(s)</span>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($juristes as $item)
            @php $u = $item['user']; $projs = $item['projects']; @endphp
            <div class="px-6 py-4 flex items-center gap-4">
                <div class="w-9 h-9 rounded-full bg-teal-100 flex items-center justify-center shrink-0">
                    <span class="text-sm font-black text-teal-600">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <p class="font-bold text-slate-900 text-sm">{{ $u->name }}</p>
                        @if($projs->count() < 2)
                            <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">Disponible</span>
                        @else
                            <span class="text-[11px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-lg">Non disponible</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $u->email }}</p>
                    @if($projs->isNotEmpty())
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        @foreach($projs as $p)
                        <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-lg truncate max-w-xs">{{ $p->title }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-sm text-slate-400 font-semibold">Aucun juriste dans cette école.</div>
            @endforelse
        </div>
    </div>

    {{-- Chefs de Projet --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-900">
            <h2 class="text-sm font-black text-white uppercase tracking-wide">Chefs de Projet</h2>
            <span class="text-xs text-slate-400 font-semibold">{{ $chefs->count() }} chef(s)</span>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($chefs as $item)
            @php $u = $item['user']; $projs = $item['projects']; @endphp
            <div class="px-6 py-4 flex items-center gap-4">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                    <span class="text-sm font-black text-blue-600">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <p class="font-bold text-slate-900 text-sm">{{ $u->name }}</p>
                        @if($projs->isEmpty())
                            <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">Disponible</span>
                        @else
                            <span class="text-[11px] font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded-lg">Non disponible</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $u->email }}</p>
                    @if($projs->isNotEmpty())
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        @foreach($projs as $p)
                        <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-lg truncate max-w-xs">{{ $p->title }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-sm text-slate-400 font-semibold">Aucun chef de projet dans cette école.</div>
            @endforelse
        </div>
    </div>

</div>

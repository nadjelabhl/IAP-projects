<div style="padding: 28px;">

    <!-- Header -->
    <div style="margin-bottom: 32px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 4px 0;">Dashboard Admin</h1>
            <p style="font-size:13px; color:#94a3b8; margin:0;">
                {{ now()->translatedFormat('l d F Y') }}
            </p>
        </div>
        <div style="background:linear-gradient(135deg,#1565C0,#0d47a1); padding:10px 20px; border-radius:12px;">
            <p style="font-size:12px; font-weight:700; color:rgba(255,255,255,0.8); margin:0 0 2px 0; text-transform:uppercase; letter-spacing:1px;">Connecté en tant que</p>
            <p style="font-size:14px; font-weight:800; color:white; margin:0;">{{ Auth::user()->name }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:32px;">

        <!-- Utilisateurs -->
        <div style="background:white; border-radius:20px; padding:28px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #f1f5f9; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-20px; right:-20px; width:100px; height:100px; background:#eff6ff; border-radius:50%;"></div>
            <div style="position:relative;">
                <div style="background:#eff6ff; width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
                    <svg style="width:24px; height:24px; color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <p style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin:0 0 8px 0;">Utilisateurs</p>
                <p style="font-size:48px; font-weight:900; color:#0f172a; margin:0; line-height:1;">{{ $totalUsers }}</p>
                <div style="display:flex; align-items:center; gap:6px; margin-top:10px;">
                    <span style="background:#dbeafe; color:#1d4ed8; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px;">Comptes actifs</span>
                </div>
            </div>
        </div>

        <!-- Projets -->
        <div style="background:white; border-radius:20px; padding:28px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #f1f5f9; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-20px; right:-20px; width:100px; height:100px; background:#fff7ed; border-radius:50%;"></div>
            <div style="position:relative;">
                <div style="background:#fff7ed; width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
                    <svg style="width:24px; height:24px; color:#f97316;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <p style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin:0 0 8px 0;">Projets</p>
                <p style="font-size:48px; font-weight:900; color:#f97316; margin:0; line-height:1;">{{ $totalProjects }}</p>
                <div style="display:flex; align-items:center; gap:6px; margin-top:10px;">
                    <span style="background:#ffedd5; color:#c2410c; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px;">En cours de suivi</span>
                </div>
            </div></div>

        <!-- Écoles -->
        <div style="background:white; border-radius:20px; padding:28px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #f1f5f9; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-20px; right:-20px; width:100px; height:100px; background:#f0fdf4; border-radius:50%;"></div>
            <div style="position:relative;">
                <div style="background:#f0fdf4; width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
                    <svg style="width:24px; height:24px; color:#22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <p style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin:0 0 8px 0;">Écoles</p>
                <p style="font-size:48px; font-weight:900; color:#22c55e; margin:0; line-height:1;">{{ $totalSchools }}</p>
                <div style="display:flex; align-items:center; gap:6px; margin-top:10px;">
                    <span style="background:#dcfce7; color:#15803d; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px;">Instituts IAP</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Projects Table -->
    <div style="background:white; border-radius:20px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:1px solid #f1f5f9; overflow:hidden;">

        <!-- Table Header -->
        <div style="padding:20px 28px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="font-size:16px; font-weight:900; color:#0f172a; margin:0;">Projets Récents</h3>
                <p style="font-size:12px; color:#94a3b8; margin:4px 0 0 0;">Derniers projets enregistrés dans le système</p>
            </div>
            <span style="background:#f1f5f9; color:#64748b; font-size:12px; font-weight:700; padding:6px 14px; border-radius:20px;">
                {{ $recentProjects->count() }} projets
            </span>
        </div>

        @if($recentProjects->count() > 0)
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="text-align:left; padding:14px 28px; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:1px;">Titre</th>
                    <th style="text-align:left; padding:14px 28px; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:1px;">Statut</th>
                    <th style="text-align:left; padding:14px 28px; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:1px;">Budget</th>
                    <th style="text-align:left; padding:14px 28px; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:1px;">École</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentProjects as $project)
                @php
                    $statusStyle = match($project->status) {
                        'En Cours'  => 'background:#dcfce7; color:#166534;',
                        'Nouveau'   => 'background:#dbeafe; color:#1e40af;',
                        'En Etude'  => 'background:#fef9c3; color:#854d0e;',
                        'Termine'   => 'background:#f1f5f9; color:#475569;',
                        default     => 'background:#f1f5f9; color:#475569;'
                    };
                @endphp
                <tr style="border-top:1px solid #f8fafc; transition:background 0.15s;"
                    onmouseover="this.style.background='#f8fafc'"onmouseout="this.style.background='white'">
                    <td style="padding:18px 28px; font-size:14px; font-weight:600; color:#1e293b;">
                        {{ $project->title }}
                    </td>
                    <td style="padding:18px 28px;">
                        <span style="padding:4px 14px; border-radius:20px; font-size:11px; font-weight:700; {{ $statusStyle }}">
                            {{ $project->status }}
                        </span>
                    </td>
                    <td style="padding:18px 28px; font-size:14px; font-weight:800; color:#0f172a;">
                        {{ number_format($project->budget, 0, ',', ' ') }}
                        <span style="font-size:11px; font-weight:600; color:#94a3b8;">DZD</span>
                    </td>
                    <td style="padding:18px 28px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:8px; height:8px; border-radius:50%; background:#1565C0;"></div>
                            <span style="font-size:13px; color:#64748b;">{{ $project->school->name ?? '-' }}</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align:center; padding:64px; color:#94a3b8;">
            <svg style="width:48px; height:48px; margin:0 auto 16px; opacity:0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p style="font-size:14px; font-weight:600;">Aucun projet récent</p>
        </div>
        @endif
    </div>

</div>
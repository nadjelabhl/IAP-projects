<div style="padding: 24px;">

    <!-- Header -->
    <div style="margin-bottom: 28px;">
        <h1 style="font-size: 26px; font-weight: 900; color: #0f172a; margin: 0 0 4px 0;">Dashboard Admin</h1>
        <p style="color: #64748b; margin: 0; font-size: 14px;">Vue d'ensemble du système IAP Gestion</p>
    </div>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px;">
        
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #f1f5f9;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 10px 0;">Utilisateurs</p>
                    <p style="font-size: 42px; font-weight: 900; color: #0f172a; margin: 0; line-height: 1;">{{ $totalUsers }}</p>
                    <p style="font-size: 12px; color: #64748b; margin: 8px 0 0 0;">comptes actifs</p>
                </div>
                <div style="background: #eff6ff; padding: 12px; border-radius: 12px;">
                    <svg style="width: 24px; height: 24px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
        </div>

        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #f1f5f9;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 10px 0;">Projets</p>
                    <p style="font-size: 42px; font-weight: 900; color: #f97316; margin: 0; line-height: 1;">{{ $totalProjects }}</p>
                    <p style="font-size: 12px; color: #64748b; margin: 8px 0 0 0;">en cours de suivi</p>
                </div>
                <div style="background: #fff7ed; padding: 12px; border-radius: 12px;">
                    <svg style="width: 24px; height: 24px; color: #f97316;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>
        </div>

        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #f1f5f9;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 10px 0;">Écoles</p>
                    <p style="font-size: 42px; font-weight: 900; color: #22c55e; margin: 0; line-height: 1;">{{ $totalSchools }}</p>
                    <p style="font-size: 12px; color: #64748b; margin: 8px 0 0 0;">instituts IAP</p>
                </div>
                <div style="background: #f0fdf4; padding: 12px; border-radius: 12px;">
                    <svg style="width: 24px; height: 24px; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
        </div>

        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.
[4/18/2026 2:20 AM] Nadjela: 06); border: 1px solid #f1f5f9;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 10px 0;">Natures</p>
                    <p style="font-size: 42px; font-weight: 900; color: #a855f7; margin: 0; line-height: 1;">{{ $totalNatures }}</p>
                    <p style="font-size: 12px; color: #64748b; margin: 8px 0 0 0;">types de projets</p>
                </div>
                <div style="background: #faf5ff; padding: 12px; border-radius: 12px;">
                    <svg style="width: 24px; height: 24px; color: #a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects Table -->
    <div style="background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #f1f5f9; overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Projets Récents</h3>
            <span style="font-size: 12px; color: #94a3b8;">{{ $recentProjects->count() }} projets</span>
        </div>

        @if($recentProjects->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc;">
                    <th style="text-align: left; padding: 14px 24px; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Titre</th>
                    <th style="text-align: left; padding: 14px 24px; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Statut</th>
                    <th style="text-align: left; padding: 14px 24px; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Budget</th>
                    <th style="text-align: left; padding: 14px 24px; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">École</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentProjects as $project)
                @php
                    $statusStyle = match($project->status) {
                        'En Cours' => 'background:#dcfce7; color:#166534;',
                        'Nouveau' => 'background:#dbeafe; color:#1e40af;',
                        'En Etude' => 'background:#fef9c3; color:#854d0e;',
                        'Termine' => 'background:#f1f5f9; color:#475569;',
                        default => 'background:#f1f5f9; color:#475569;'
                    };
                @endphp
                <tr style="border-top: 1px solid #f1f5f9;">
                    <td style="padding: 16px 24px; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $project->title }}</td>
                    <td style="padding: 16px 24px;">
                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; {{ $statusStyle }}">
                            {{ $project->status }}
                        </span>
                    </td>
                    <td style="padding: 16px 24px; font-size: 14px; font-weight: 700; color: #0f172a;">{{ number_format($project->budget, 0, ',', ' ') }} DZD</td>
                    <td style="padding: 16px 24px; font-size: 14px; color: #64748b;">{{ $project->school->name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align: center; padding: 48px; color: #94a3b8;">
[4/18/2026 2:20 AM] Nadjela: <p style="font-size: 14px;">Aucun projet récent</p>
        </div>
        @endif
    </div>
</div>
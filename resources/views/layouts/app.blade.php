<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>{{ config('app.name', 'IAP Gestion') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body style="font-family: 'Inter', sans-serif; background: #f1f5f9; margin: 0;">

    <div style="display: flex; min-height: 100vh;">

        @include('livewire.layout.navigation')

        <div style="flex: 1; display: flex; flex-direction: column; margin-left: 260px;">

            @if(isset($header))
                <header style="background: linear-gradient(135deg, #0f172a, #1e3a5f); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 30; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                    <h1 style="font-size: 18px; font-weight: 800; color: white; text-transform: uppercase; letter-spacing: 1px; margin: 0;">
                        {{ $header }}
                    </h1>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; display: inline-block;"></span>
                        <span style="font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 2px;">Système Actif</span>
                    </div>
                </header>
            @endif

            <main style="flex: 1; padding: 32px;">
                {{ $slot }}
            </main>

            <footer style="background: linear-gradient(135deg, #0f172a, #1e3a5f); padding: 16px 32px; text-align: center;">
                <p style="font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 2px; margin: 0;">
                    &copy; 2026 SONATRACH - IAP
                </p>
            </footer>
        </div>
    </div>

    @livewireScripts
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'IAP PFE') }} - Authentification</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        @livewireStyles

        <style>
            body {
                font-family: 'Poppins', sans-serif;
            }
            /* Empêche Alpine.js de clignoter au chargement */
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased bg-iap-blue text-slate-900">
        
        <main>
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
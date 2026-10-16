<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-sky-50 py-10 px-4">
            <!-- Brand -->
            <a href="/" class="mb-8 flex items-center gap-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-200">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l1.57.393A9.065 9.065 0 0 1 12 21a9.065 9.065 0 0 1-9.37-5.307L5 14.5m14.8.8 1.402 1.402c1.232-1.906 1.651-4.225.876-6.45M5 14.5l-1.402 1.402c-.75-2.123-.37-4.382.876-6.45" />
                    </svg>
                </span>
                <span class="text-2xl font-bold tracking-tight text-gray-900">Helpdesk IT</span>
            </a>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl shadow-indigo-100/70 ring-1 ring-gray-100 overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>

            <p class="mt-6 text-sm text-gray-400">Sistem Tiket Helpdesk Internal</p>
        </div>
    </body>
</html>

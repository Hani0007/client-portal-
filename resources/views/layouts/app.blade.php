<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @livewireStyles
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
            <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <a href="{{ route('home') }}" class="text-xl font-semibold text-slate-900">ProjectHub</a>
                    <p class="text-sm text-slate-500">Streamline project management, client collaboration, and invoicing</p>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-slate-900 px-4 py-2 text-white transition hover:bg-slate-700">Dashboard</a>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-slate-700 transition hover:bg-slate-100">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md bg-slate-900 px-4 py-2 text-white transition hover:bg-slate-700">Login</a>
                    @endauth
                </div>
            </header>

            <main>
                @yield('content')
            </main>
        </div>
        @include('stripe-checkout-js')
        @livewireScripts
    </body>
</html>

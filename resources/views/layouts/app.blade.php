<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
        <title>{{ $title ?? config('app.name') }}</title>
 
        @vite(['resources/css/app.css', 'resources/js/app.js'])
 
        @livewireStyles
    </head>
    <body class="font-sans antialiased text-slate-900">
        <div class="h-screen flex overflow-hidden bg-slate-50" x-data="{ sidebarOpen: false }">
            @include('layouts.sidebar')

            <main class="flex-1 flex flex-col h-screen overflow-hidden">
                @include('layouts.topbar', ['title' => $title ?? null])
                
                <div class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6 lg:p-10">
                    {{ $slot }}
                </div>
            </main>
        </div>
 
        @livewireScripts
    </body>
</html>
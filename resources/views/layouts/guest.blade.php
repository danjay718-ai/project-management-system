<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS Vite Compilation -->
    @vite(['resources/css/app.css'])
    
    <!-- Livewire Core Styles -->
    @livewireStyles
</head>
<body class="h-full font-sans antialiased bg-gray-50 text-gray-900">
    <!-- 
        The Login and Register views are now fully responsive, 
        full-screen components. We just need the slot here! 
    -->
    {{ $slot }}

    <!-- Livewire Core Scripts -->
    @livewireScripts
</body>
</html>

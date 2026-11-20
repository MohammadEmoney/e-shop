<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? '' }} - {{ config('app.name', 'مدیریت') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#F1F5F9]">

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        {{-- Content area --}}
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            {{-- Topbar --}}
            @include('admin.partials.topbar')

            <main class="p-4 md:p-6 2xl:p-10">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
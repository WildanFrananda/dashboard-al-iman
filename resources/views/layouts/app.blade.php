<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F5F5F5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SIAKMAN Dashboard' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full antialiased font-sans text-gray-900 bg-[#F5F5F5]" x-data="{ sidebarOpen: false }">

    <!-- MAIN CONTAINER -->
    <!-- Padding luar agar konten tidak menempel ke tepi layar -->
    <div class="min-h-screen p-4 md:p-6 lg:p-8 max-w-[1600px] mx-auto flex flex-col gap-6">

        <!-- 1. HEADER (Floating Rounded Bar) -->
        <!-- Header sekarang ada DI DALAM container agar lebarnya sama dengan konten -->
        <header class="bg-white rounded-[20px] shadow-sm px-6 py-4 flex items-center justify-between relative z-30">
            
            <!-- LEFT: LOGO -->
            <div class="flex items-center gap-4">
                <!-- Mobile Menu Trigger -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-1 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <div class="flex items-center">
                    <img src="{{ asset('img/siakman.png') }}" alt="Siakman Logo" class="h-10 w-auto md:h-16 lg:h-[50px] object-contain">
                </div>
            </div>

            <!-- CENTER: WELCOME MESSAGE (Hidden on Mobile) -->
            <div class="hidden md:flex flex-col items-center absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 w-max">
                 <h1 class="font-heading font-bold text-lg text-gray-900">Selamat Datang Di SIAKMAN</h1>
                 <p class="text-xs text-gray-500 font-medium">(Sistem Akademik Al-Iman)</p>
            </div>

            <!-- RIGHT: PROFILE -->
            <div class="flex items-center gap-4">
                 <div class="hidden sm:block text-right">
                    <p class="text-sm font-bold text-gray-900 leading-tight">Alila Nafisah</p>
                    <p class="text-xs text-gray-500 font-medium">Murid</p>
                 </div>
                 <div class="relative">
                    <img class="h-10 w-10 md:h-11 md:w-11 rounded-full border-2 border-white shadow-sm object-cover bg-blue-500" 
                         src="https://placehold.co/100x100/3b82f6/white?text=AN" 
                         alt="Profile">
                    <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-green-400"></span>
                 </div>
            </div>
        </header>

        <!-- 2. CONTENT WRAPPER (Sidebar + Main) -->
        <div class="flex flex-col lg:flex-row gap-6 items-start flex-1">
            
            <!-- SIDEBAR DESKTOP -->
            <aside class="hidden lg:block w-[280px] flex-shrink-0">
                <div class="bg-white rounded-[20px] shadow-sm p-6 min-h-[600px]">
                    <nav class="space-y-4">
                        @include('layouts.partials.sidebar-nav')
                    </nav>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 w-full min-w-0">
                {{ $slot }}
            </main>

        </div>
    </div>

    <!-- MOBILE MENU -->
    <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" 
             @click="sidebarOpen = false"></div>

        <div class="fixed inset-0 flex">
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="relative mr-16 flex w-full max-w-xs flex-1 bg-white p-6 shadow-xl">
                
                <div class="flex flex-col w-full h-full">
                    <div class="flex items-center justify-between mb-8">
                        <span class="font-heading font-bold text-xl text-gray-900">Menu</span>
                        <button type="button" @click="sidebarOpen = false" class="-m-2.5 p-2.5 text-gray-500">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <nav class="flex flex-1 flex-col">
                        <ul role="list" class="-mx-2 space-y-4">
                            @include('layouts.partials.sidebar-nav')
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
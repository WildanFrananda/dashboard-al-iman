<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Al-Iman School' }}</title>

    <!-- Fonts: Inter & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="h-full antialiased text-gray-900 font-sans">

    <div class="min-h-screen w-full flex">

        <!-- LEFT SIDE: Illustration & Branding -->
        <!-- bg-brand-600 ditambahkan agar background biru tetap ada di belakang gambar -->
        <div class="hidden lg:flex lg:w-1/2 relative items-center justify-center overflow-hidden">

            <!-- LOGO FIXED -->
            <!-- top-8 left-8: Memberikan padding dari pojok kiri atas -->
            <!-- z-20: Memastikan logo selalu di atas gambar -->
            <div class="absolute top-8 left-8 z-20">
                <img src="{{ asset('img/white-logo.png') }}" alt="Al-Iman Logo"
                    class="h-12 w-auto object-contain">
            </div>

            <!-- DYNAMIC IMAGE LOGIC -->
            <!-- Jika halaman mengirim slot 'illustration' (seperti forgot password), pakai itu. -->
            <!-- Jika tidak, pakai default login.png -->
            <div class="relative z-10 w-full h-full flex">
                @if(isset($illustration))
                {{ $illustration }}
                @else
                <!-- Default Image (Login) -->
                <!-- Gunakan object-contain agar gambar pas di tengah -->
                <img src="{{ asset('img/login.png') }}" alt="School Illustration"
                    class="w-full h-full object-contain drop-shadow-2xl">
                @endif
            </div>
        </div>

        <!-- RIGHT SIDE: Form Container -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 py-12 lg:px-16 xl:px-24 bg-gray-50/50">
            <!-- Mobile Logo -->
            <div class="lg:hidden mb-8 flex justify-center">
                <img src="{{ asset('img/siakman.png') }}" alt="Al-Iman Logo"
                    class="h-10 w-auto">
            </div>

            <div class="w-full max-w-md mx-auto">
                {{ $slot }}
            </div>
        </div>

    </div>

    @livewireScripts
</body>

</html>
<div class="space-y-6">
    
    <!-- 1. SUMMARY CARDS SECTION -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        
        <!-- Card: Hadir (Green) -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-green-100 p-3 rounded-full flex-shrink-0 text-green-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Hadir</p>
                <p class="text-xl font-bold text-gray-900">{{ $summary['hadir'] }}</p>
            </div>
        </div>

        <!-- Card: Izin (Blue) -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-blue-100 p-3 rounded-full flex-shrink-0 text-blue-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Izin</p>
                <p class="text-xl font-bold text-gray-900">{{ $summary['izin'] }}</p>
            </div>
        </div>

        <!-- Card: Sakit (Yellow) -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-yellow-100 p-3 rounded-full flex-shrink-0 text-yellow-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Sakit</p>
                <p class="text-xl font-bold text-gray-900">{{ $summary['sakit'] }}</p>
            </div>
        </div>

        <!-- Card: Alfa (Red) -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-red-100 p-3 rounded-full flex-shrink-0 text-red-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Alfa</p>
                <p class="text-xl font-bold text-gray-900">{{ $summary['alfa'] }}</p>
            </div>
        </div>

        <!-- Legend Card (Full White) -->
        <div class="bg-white rounded-2xl p-3 shadow-sm border border-gray-100 flex flex-col justify-center text-[10px] text-gray-500 font-medium">
            <p class="font-bold text-gray-900 mb-1 text-xs">Keterangan</p>
            <div class="grid grid-cols-2 gap-x-2 gap-y-0.5">
                <span>H = Hadir</span>
                <span>S = Sakit</span>
                <span>I = Izin</span>
                <span>A = Alfa</span>
            </div>
        </div>
    </div>

    <!-- 2. ATTENDANCE HISTORY LIST -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden min-h-[500px]">
        
        <!-- Header List -->
        <!-- Menambahkan border-b agar terpisah jelas dari konten -->
        <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="font-bold text-gray-900 text-sm md:text-base">Mata Pelajaran</div>
            <div class="font-bold text-gray-900 text-sm md:text-base md:col-span-2">Pertemuan</div>
        </div>

        <!-- List Items -->
        <!-- UPDATE: Menggunakan divide-y, divide-dashed, dan divide-gray-300 agar garis terlihat jelas -->
        <div class="divide-y divide-dashed divide-gray-300">
            @foreach($attendanceRecords as $record)
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4 items-center hover:bg-gray-50/50 transition-colors">
                
                <!-- Subject Name -->
                <div class="font-medium text-gray-800 text-sm md:text-base">
                    {{ $record['subject'] }}
                </div>

                <!-- Badges Container -->
                <!-- Horizontal scroll on mobile if overflow -->
                <div class="md:col-span-2 flex flex-wrap gap-2 overflow-x-auto pb-1 md:pb-0">
                    @foreach($record['history'] as $history)
                        <!-- Dynamic Color Logic -->
                        @php
                            $colorClass = match($history['status']) {
                                'H' => 'bg-green-100 text-green-700',
                                'I' => 'bg-blue-100 text-blue-700',
                                'S' => 'bg-yellow-100 text-yellow-700',
                                'A' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp

                        <div class="{{ $colorClass }} px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 min-w-[50px] justify-center shadow-sm border border-white">
                            <span class="opacity-60">{{ $history['meet'] }}.</span>
                            <span>{{ $history['status'] }}</span>
                        </div>
                    @endforeach
                </div>

            </div>
            @endforeach
        </div>
        
        <!-- Empty Space Filler (Optional for visual consistency) -->
        <div class="h-full bg-white flex-1"></div>

    </div>
</div>
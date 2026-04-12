<div class="space-y-6">
    
    <!-- 1. Hero Banner Section -->
    <div class="overflow-hidden rounded-3xl shadow-md">
        <img src="{{ asset('img/dashboard.webp') }}"
             alt="Hero Banner Al-Iman"
             class="w-full object-cover">
    </div>

    <!-- 2. Statistics Grid -->
    <!-- Grid: 1 col mobile, 2 col tablet, 5 col desktop -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach($stats as $stat)
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4 transition-transform hover:-translate-y-1">
            <!-- Icon Wrapper -->
            <div class="{{ $stat['icon_bg'] }} p-3 rounded-full flex-shrink-0">
                <!-- Simple SVG switch based on icon name -->
                @if($stat['icon'] === 'users')
                    <svg class="w-6 h-6 {{ $stat['icon_color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                @elseif($stat['icon'] === 'teacher')
                     <svg class="w-6 h-6 {{ $stat['icon_color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                @elseif($stat['icon'] === 'calendar')
                     <svg class="w-6 h-6 {{ $stat['icon_color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                @elseif($stat['icon'] === 'target')
                     <svg class="w-6 h-6 {{ $stat['icon_color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                @else
                     <svg class="w-6 h-6 {{ $stat['icon_color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @endif
            </div>
            
            <!-- Text Info -->
            <div>
                <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">{{ $stat['title'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $stat['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- 3. Jadwal & Kalender Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Jadwal Pembelajaran (Takes up 2/3 width on large screens) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Jadwal Pembelajaran - Kelas 6A</h3>
                <!-- Arrows Controls (Visual Only) -->
                <div class="flex gap-2">
                    <button class="p-1 bg-gray-200 rounded hover:bg-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                    <button class="p-1 bg-gray-800 text-white rounded hover:bg-gray-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                </div>
            </div>

            <!-- Scrollable Container for Mobile -->
            <div class="flex overflow-x-auto gap-4 pb-4 md:grid md:grid-cols-2 md:pb-0 scrollbar-hide">
                @foreach($schedule as $item)
                <div class="min-w-[250px] bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex-shrink-0">
                    <!-- Card Header -->
                    <div class="bg-[#006C9C] px-4 py-2 text-white font-bold text-sm">
                        {{ $item['day'] }}
                    </div>
                    <!-- Card Body -->
                    <div class="p-4 space-y-4">
                        @foreach($item['lessons'] as $lesson)
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $lesson['subject'] }}</p>
                                <p class="text-[10px] text-gray-500">{{ $lesson['teacher'] }}</p>
                            </div>
                            <div class="text-xs font-medium text-gray-600 bg-gray-50 px-2 py-1 rounded">
                                {{ $lesson['time'] }}
                            </div>
                        </div>
                        <!-- Divider if not last -->
                        @if(!$loop->last) <hr class="border-gray-100"> @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Bottom Charts Area -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Attendance Chart -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 min-h-[200px]">
                    <h4 class="text-sm font-bold text-gray-900 mb-4">Tingkat Kehadiran Mingguan (%)</h4>
                    <!-- Chart Placeholder -->
                    <div class="relative h-32 w-full border-b border-l border-gray-200 flex items-end justify-around px-2">
                         <!-- Dummy Bars -->
                         <div class="w-8 bg-blue-100 h-[80%] rounded-t-sm"></div>
                         <div class="w-8 bg-blue-100 h-[100%] rounded-t-sm"></div>
                         <div class="w-8 bg-blue-100 h-[90%] rounded-t-sm"></div>
                         <div class="w-8 bg-blue-100 h-[85%] rounded-t-sm"></div>
                         <div class="w-8 bg-blue-100 h-[95%] rounded-t-sm"></div>
                    </div>
                    <div class="flex justify-around text-[10px] text-gray-400 mt-2">
                        <span>Senin</span><span>Selasa</span><span>Rabu</span><span>Kamis</span><span>Jumat</span>
                    </div>
                </div>

                <!-- Academic Info -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 min-h-[200px]">
                    <h4 class="text-sm font-bold text-[#006C9C] mb-2">Informasi Pengembangan <br> Akademik Siswa</h4>
                    <p class="text-xs text-gray-500 leading-relaxed text-justify">
                        Kamu telah menunjukkan hasil belajar yang sangat baik! Pertahankan semangat belajar ini dengan terus mengulang pelajaran di rumah, aktif bertanya di kelas, dan berpartisipasi dalam kegiatan sekolah. Konsistensi adalah kunci.
                    </p>
                </div>
            </div>
        </div>

        <!-- Kalender Akademik (Right Side) -->
        @livewire('academic-calendar')

    </div>

</div>
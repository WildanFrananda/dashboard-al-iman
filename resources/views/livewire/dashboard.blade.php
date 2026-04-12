<div class="space-y-6">

    <!-- 1. Hero Banner Section -->
    <div class="overflow-hidden rounded-3xl shadow-md">
        <img src="{{ asset('img/dashboard.webp') }}"
             alt="Hero Banner Al-Iman"
             class="w-full object-cover">
    </div>

    <!-- 2. Statistics Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach($stats as $stat)
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4 transition-transform hover:-translate-y-1">
            <div class="{{ $stat['icon_bg'] }} p-3 rounded-full flex-shrink-0">
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
            <div>
                <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">{{ $stat['title'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $stat['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- 3. Main Content Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left/Center Panel (2/3 width) -->
        <div class="lg:col-span-2 space-y-4">

            @if(Auth::user()->role === 'admin')
            {{-- ── ADMIN: Rekap Kelas ──────────────────────────────────────── --}}

            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Rekap Kelas Tahun Ajaran</h3>
                <a href="{{ route('manage-class') }}"
                   class="text-xs text-[#0F609B] hover:underline font-medium">
                    Kelola Kelas →
                </a>
            </div>

            @if(count($kelasRecap) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($kelasRecap as $kelas)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-[#0F609B] px-4 py-2 flex items-center justify-between">
                        <span class="text-white font-bold text-sm">{{ $kelas['nama'] }}</span>
                        <span class="text-xs text-blue-200 font-mono">{{ $kelas['kode'] }}</span>
                    </div>
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Jumlah Murid</span>
                            <span class="text-sm font-bold text-gray-900">{{ $kelas['jumlah_murid'] }} siswa</span>
                        </div>
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-500 flex-shrink-0">Wali Kelas</span>
                            <span class="text-xs font-medium text-gray-700 text-right">{{ $kelas['wali_kelas'] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="text-sm text-gray-400">Belum ada kelas yang terdaftar</p>
            </div>
            @endif

            @else
            {{-- ── GURU & MURID: Jadwal Pembelajaran ──────────────────────── --}}

            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $scheduleTitle }}</h3>
                @if(Auth::user()->role === 'guru')
                <a href="{{ route('manage-schedule') }}"
                   class="text-xs text-[#0F609B] hover:underline font-medium">
                    Kelola Jadwal →
                </a>
                @endif
            </div>

            @if(count($schedule) > 0)
            <div class="flex overflow-x-auto gap-4 pb-4 md:grid md:grid-cols-2 md:pb-0 scrollbar-hide">
                @foreach($schedule as $item)
                <div class="min-w-[250px] bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex-shrink-0">
                    <div class="bg-[#0F609B] px-4 py-2 text-white font-bold text-sm">
                        {{ $item['day'] }}
                    </div>
                    <div class="p-4 space-y-4">
                        @foreach($item['lessons'] as $lesson)
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $lesson['subject'] }}</p>
                                <p class="text-[10px] text-gray-500">{{ $lesson['teacher'] }}</p>
                            </div>
                            <div class="text-xs font-medium text-gray-600 bg-gray-50 px-2 py-1 rounded whitespace-nowrap">
                                {{ $lesson['time'] }}
                            </div>
                        </div>
                        @if(!$loop->last) <hr class="border-gray-100"> @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-gray-400">Belum ada jadwal pembelajaran</p>
            </div>
            @endif
            @endif

            <!-- Bottom Info Cards (sama untuk semua role) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Attendance Chart (real data) -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 min-h-[200px]">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-gray-900">Tingkat Kehadiran Minggu Ini</h4>
                        @php
                            $validDays = collect($weeklyAttendance)->whereNotNull('percentage');
                            $avgPct    = $validDays->count() > 0
                                ? (int) round($validDays->avg('percentage'))
                                : null;
                        @endphp
                        @if($avgPct !== null)
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ $avgPct >= 80 ? 'bg-green-100 text-green-700' : ($avgPct >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            Rata-rata {{ $avgPct }}%
                        </span>
                        @endif
                    </div>

                    @php $hasAnyData = collect($weeklyAttendance)->whereNotNull('percentage')->isNotEmpty(); @endphp

                    @if($hasAnyData)
                    {{-- Bar chart --}}
                    <div class="relative h-32 w-full border-b border-l border-gray-200 flex items-end justify-around px-2 gap-1">
                        @foreach($weeklyAttendance as $day)
                        @php
                            $pct      = $day['percentage'];
                            $height   = $pct !== null ? max(4, $pct) : 0;
                            $barColor = $pct === null
                                ? 'bg-gray-100'
                                : ($pct >= 80 ? 'bg-[#0F609B]' : ($pct >= 60 ? 'bg-yellow-400' : 'bg-red-400'));
                        @endphp
                        <div class="flex flex-col items-center justify-end h-full flex-1 gap-1 group relative">
                            @if($pct !== null)
                            {{-- Tooltip --}}
                            <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 hidden group-hover:flex
                                        bg-gray-800 text-white text-[10px] rounded px-1.5 py-0.5 whitespace-nowrap z-10 pointer-events-none">
                                {{ $day['hadir'] }}/{{ $day['total'] }} hadir ({{ $pct }}%)
                            </div>
                            @endif
                            <div class="{{ $barColor }} w-full rounded-t-sm transition-all duration-500"
                                 style="height: {{ $height }}%"></div>
                        </div>
                        @endforeach
                    </div>
                    {{-- Hari labels + angka --}}
                    <div class="flex justify-around mt-2 gap-1">
                        @foreach($weeklyAttendance as $day)
                        <div class="flex-1 text-center">
                            <p class="text-[10px] text-gray-400">{{ $day['day'] }}</p>
                            <p class="text-[10px] font-semibold {{ $day['percentage'] !== null ? 'text-gray-700' : 'text-gray-300' }}">
                                {{ $day['percentage'] !== null ? $day['percentage'].'%' : '-' }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center h-32 text-gray-400 text-xs gap-2">
                        <svg class="w-8 h-8 text-gray-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                        Belum ada data absensi minggu ini
                    </div>
                    @endif
                </div>

                <!-- Academic Info -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 min-h-[200px]">
                    <h4 class="text-sm font-bold text-[#0F609B] mb-2">Informasi Pengembangan <br> Akademik Siswa</h4>
                    <p class="text-xs text-gray-500 leading-relaxed text-justify">
                        Semangat belajar yang konsisten adalah kunci keberhasilan. Terus ulang pelajaran di rumah,
                        aktif bertanya di kelas, dan berpartisipasi dalam kegiatan sekolah untuk meraih prestasi terbaik.
                    </p>
                </div>
            </div>

        </div>

        <!-- Kalender Akademik (Right Side) -->
        @livewire('academic-calendar')

    </div>

</div>

<div class="flex flex-col gap-6 h-full">

    <!-- FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 shrink-0">
        <div class="flex flex-col md:flex-row items-end gap-4">

            <!-- Bulan -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/80 text-xs font-medium uppercase tracking-wide">Bulan</label>
                <select wire:model.live="bulan"
                    class="min-w-[150px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
                    @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $nama)
                        <option value="{{ $num }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tahun -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/80 text-xs font-medium uppercase tracking-wide">Tahun</label>
                <select wire:model.live="tahun"
                    class="min-w-[110px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
                    @for($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Kelas -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/80 text-xs font-medium uppercase tracking-wide">Kelas</label>
                <select wire:model.live="kelasId"
                    class="min-w-[180px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
                    <option value="">Semua Kelas</option>
                    @foreach($this->kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Info label -->
            <div class="hidden md:block flex-1"></div>
            <div class="text-white/70 text-xs text-right leading-tight">
                Data diperbarui otomatis<br>saat filter berubah.
            </div>
        </div>
    </div>

    <!-- SUMMARY TOTAL ROW -->
    @php
        $rekap     = $this->rekapData;
        $totalRows = count($rekap);
        $sumHadir  = collect($rekap)->sum('hadir');
        $sumIzin   = collect($rekap)->sum('izin');
        $sumSakit  = collect($rekap)->sum('sakit');
        $sumAlpa   = collect($rekap)->sum('alpa');
        $sumTotal  = collect($rekap)->sum('total');
        $avgPct    = $sumTotal > 0 ? round(($sumHadir / $sumTotal) * 100, 1) : null;
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 shrink-0">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-green-100 p-3 rounded-full flex-shrink-0 text-green-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Total Hadir</p>
                <p class="text-xl font-bold text-gray-900">{{ $sumHadir }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-amber-100 p-3 rounded-full flex-shrink-0 text-amber-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Total Izin</p>
                <p class="text-xl font-bold text-gray-900">{{ $sumIzin }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-blue-100 p-3 rounded-full flex-shrink-0 text-blue-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Total Sakit</p>
                <p class="text-xl font-bold text-gray-900">{{ $sumSakit }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-red-100 p-3 rounded-full flex-shrink-0 text-red-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Total Alfa</p>
                <p class="text-xl font-bold text-gray-900">{{ $sumAlpa }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-purple-100 p-3 rounded-full flex-shrink-0 text-purple-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Rata-rata Hadir</p>
                <p class="text-xl font-bold text-gray-900">
                    {{ $avgPct !== null ? $avgPct.'%' : '-' }}
                </p>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-[20px] shadow-sm flex-1 flex flex-col border border-gray-100 overflow-hidden">

        <!-- Table Head -->
        <div class="bg-[#F1F5F9] px-6 py-3 shrink-0">
            <div class="grid grid-cols-9 gap-2 text-xs font-bold text-gray-600 uppercase tracking-wide">
                <div class="col-span-2">Nama Murid</div>
                <div>Kelas</div>
                <div class="text-center text-green-700">Hadir</div>
                <div class="text-center text-amber-700">Izin</div>
                <div class="text-center text-blue-700">Sakit</div>
                <div class="text-center text-red-700">Alfa</div>
                <div class="text-center text-gray-700">Total</div>
                <div class="text-center text-purple-700">% Hadir</div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto">
            @forelse($rekap as $row)
                @php
                    $pct = $row['persentase'];
                    $pctColor = match(true) {
                        $pct === null          => 'text-gray-400',
                        $pct >= 80             => 'text-green-600 font-semibold',
                        $pct >= 60             => 'text-amber-600 font-semibold',
                        default                => 'text-red-600 font-bold',
                    };
                @endphp
                <div class="px-6 py-3 grid grid-cols-9 gap-2 items-center text-sm hover:bg-gray-50/60 transition-colors">
                    <div class="col-span-2 font-medium text-gray-800 truncate">{{ $row['nama'] }}</div>
                    <div class="text-gray-500 text-xs truncate">{{ $row['kelas'] }}</div>
                    <div class="text-center">
                        <span class="inline-block bg-green-100 text-green-700 font-bold px-2 py-0.5 rounded text-xs min-w-[28px]">
                            {{ $row['hadir'] }}
                        </span>
                    </div>
                    <div class="text-center">
                        <span class="inline-block bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded text-xs min-w-[28px]">
                            {{ $row['izin'] }}
                        </span>
                    </div>
                    <div class="text-center">
                        <span class="inline-block bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded text-xs min-w-[28px]">
                            {{ $row['sakit'] }}
                        </span>
                    </div>
                    <div class="text-center">
                        <span class="inline-block bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded text-xs min-w-[28px]">
                            {{ $row['alpa'] }}
                        </span>
                    </div>
                    <div class="text-center text-gray-600 font-medium">{{ $row['total'] }}</div>
                    <div class="text-center {{ $pctColor }} text-xs">
                        {{ $pct !== null ? $pct.'%' : '-' }}
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center text-gray-400 text-sm">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Tidak ada data absensi untuk periode ini.
                </div>
            @endforelse
        </div>

        <!-- Footer row count -->
        @if($totalRows > 0)
        <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-100 text-xs text-gray-400 shrink-0">
            Menampilkan {{ $totalRows }} murid
        </div>
        @endif
    </div>

</div>

<div class="flex flex-col gap-6 h-full">

    <!-- FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 shrink-0">
        <div class="flex flex-col md:flex-row items-end gap-4">

            <!-- Semester -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/70 text-xs font-medium uppercase tracking-wide">Semester</label>
                <select wire:model.live="semester"
                    class="min-w-[140px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                </select>
            </div>

            <!-- Tahun Ajaran -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/70 text-xs font-medium uppercase tracking-wide">Tahun Ajaran</label>
                <input type="text" wire:model.live.debounce.600ms="tahunAjaran" placeholder="2025/2026"
                    class="w-full md:w-[120px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
            </div>

            <div class="hidden md:block flex-1"></div>

            <!-- Info -->
            <div class="text-white/60 text-xs text-right leading-relaxed">
                Nilai ditampilkan otomatis<br>setelah guru menginput.
            </div>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    @php $summary = $this->summary; @endphp
    <div class="grid grid-cols-3 gap-4 shrink-0">

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-blue-100 p-3 rounded-full shrink-0 text-blue-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Rata-rata UTS</p>
                <p class="text-2xl font-bold text-gray-900">
                    {{ $summary['rata_uts'] !== null ? $summary['rata_uts'] : '—' }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-purple-100 p-3 rounded-full shrink-0 text-purple-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Rata-rata UAS</p>
                <p class="text-2xl font-bold text-gray-900">
                    {{ $summary['rata_uas'] !== null ? $summary['rata_uas'] : '—' }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-green-100 p-3 rounded-full shrink-0 text-green-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Mata Pelajaran</p>
                <p class="text-2xl font-bold text-gray-900">{{ $summary['mapel'] }}</p>
            </div>
        </div>
    </div>

    <!-- TABEL NILAI -->
    <div class="bg-white rounded-[20px] shadow-sm flex-1 flex flex-col border border-gray-100 overflow-hidden">

        <!-- Header -->
        <div class="bg-[#F1F5F9] px-6 py-3.5 shrink-0 border-b border-gray-200">
            <div class="grid grid-cols-10 gap-3 text-xs font-bold text-gray-600 uppercase tracking-wide">
                <div class="col-span-4">Mata Pelajaran</div>
                <div class="col-span-2 text-center text-blue-700">UTS</div>
                <div class="col-span-2 text-center text-purple-700">UAS</div>
                <div class="col-span-2 text-center text-gray-500">Keterangan</div>
            </div>
        </div>

        <!-- Body -->
        <div class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto">
            @forelse($this->gradeRecords as $record)
                @php
                    $utsColor = match(true) {
                        $record['uts'] === null => 'text-gray-400',
                        $record['uts'] >= 75    => 'text-green-600 font-semibold',
                        $record['uts'] >= 60    => 'text-amber-600 font-semibold',
                        default                 => 'text-red-600 font-bold',
                    };
                    $uasColor = match(true) {
                        $record['uas'] === null => 'text-gray-400',
                        $record['uas'] >= 75    => 'text-green-600 font-semibold',
                        $record['uas'] >= 60    => 'text-amber-600 font-semibold',
                        default                 => 'text-red-600 font-bold',
                    };
                @endphp
                <div class="px-6 py-4 grid grid-cols-10 gap-3 items-center hover:bg-gray-50/50 transition-colors">

                    <div class="col-span-4 font-medium text-gray-800 text-sm">{{ $record['subject'] }}</div>

                    <!-- UTS Badge -->
                    <div class="col-span-2 flex justify-center">
                        @if($record['uts'] !== null)
                            <span class="inline-flex items-center justify-center w-14 h-9 rounded-xl text-sm font-bold
                                {{ $record['uts'] >= 75 ? 'bg-green-100 text-green-700' : ($record['uts'] >= 60 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ $record['uts'] }}
                            </span>
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </div>

                    <!-- UAS Badge -->
                    <div class="col-span-2 flex justify-center">
                        @if($record['uas'] !== null)
                            <span class="inline-flex items-center justify-center w-14 h-9 rounded-xl text-sm font-bold
                                {{ $record['uas'] >= 75 ? 'bg-green-100 text-green-700' : ($record['uas'] >= 60 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ $record['uas'] }}
                            </span>
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </div>

                    <!-- Keterangan -->
                    <div class="col-span-2 text-xs text-gray-500 truncate text-center">
                        {{ $record['keterangan'] ?? '—' }}
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center text-sm text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Belum ada nilai untuk semester dan tahun ajaran ini.
                </div>
            @endforelse
        </div>

        <!-- Keterangan Warna -->
        <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-100 shrink-0 flex items-center gap-6 text-xs text-gray-500">
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm bg-green-200"></span> ≥ 75 Baik
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm bg-amber-200"></span> 60–74 Cukup
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm bg-red-200"></span> &lt; 60 Perlu Perhatian
            </span>
        </div>
    </div>

</div>

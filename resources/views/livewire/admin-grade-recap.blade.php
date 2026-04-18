<div class="flex flex-col gap-6 h-full">

    <!-- FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 shrink-0">
        <div class="flex flex-col md:flex-row items-end gap-4">

            <!-- Filter Kelas -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/70 text-xs font-medium uppercase tracking-wide">Kelas</label>
                <select wire:model.live="kelasId"
                    class="min-w-[180px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
                    <option value="">Semua Kelas</option>
                    @foreach($this->kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

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
            <div class="text-white/60 text-xs text-right leading-relaxed">
                Filter diterapkan otomatis.<br>Data diperbarui real-time.
            </div>
        </div>
    </div>

    <!-- MAIN TABLE -->
    @php $rekap = $this->rekapData; @endphp
    <div class="bg-white rounded-[20px] shadow-sm flex-1 flex flex-col border border-gray-100 overflow-hidden">

        <!-- Table Head -->
        <div class="bg-[#F1F5F9] px-6 py-3.5 shrink-0 border-b border-gray-200">
            <div class="flex items-center text-xs font-bold text-gray-600 uppercase tracking-wide gap-4">
                <div class="w-8 text-center">#</div>
                <div class="w-28 shrink-0">NIS</div>
                <div class="flex-1">Nama Murid</div>
                <div class="w-24 shrink-0">Kelas</div>
                <div class="flex-1">Nilai per Mata Pelajaran</div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto">
            @forelse($rekap as $i => $row)
                <div class="px-6 py-4 flex items-start gap-4 hover:bg-gray-50/50 transition-colors">

                    <!-- No -->
                    <div class="w-8 text-center text-xs text-gray-400 font-medium pt-0.5">{{ $i + 1 }}</div>

                    <!-- NIS -->
                    <div class="w-28 shrink-0 text-xs text-gray-500 font-mono pt-0.5">{{ $row['nis'] }}</div>

                    <!-- Nama -->
                    <div class="flex-1 font-medium text-gray-800 text-sm pt-0.5">{{ $row['nama'] }}</div>

                    <!-- Kelas -->
                    <div class="w-24 shrink-0 pt-0.5">
                        <span class="inline-block bg-blue-50 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded">
                            {{ $row['kelas'] }}
                        </span>
                    </div>

                    <!-- Nilai per Mapel -->
                    <div class="flex-1">
                        @if(empty($row['subjects']))
                            <span class="text-xs text-gray-300 italic">Belum ada nilai</span>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach($row['subjects'] as $subj)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs">
                                        <div class="font-semibold text-gray-700 mb-1 truncate max-w-[120px]">{{ $subj['subject'] }}</div>
                                        <div class="flex gap-2">
                                            @if($subj['uts'] !== null)
                                                <span class="flex items-center gap-0.5">
                                                    <span class="text-blue-500 font-bold">UTS</span>
                                                    <span class="text-gray-700 font-bold">
                                                        {{ $subj['uts'] }}
                                                    </span>
                                                </span>
                                            @endif
                                            @if($subj['uas'] !== null)
                                                <span class="flex items-center gap-0.5">
                                                    <span class="text-purple-500 font-bold">UAS</span>
                                                    <span class="text-gray-700 font-bold">
                                                        {{ $subj['uas'] }}
                                                    </span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center text-sm text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Tidak ada data nilai untuk filter yang dipilih.
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if(count($rekap) > 0)
        <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-100 shrink-0 flex items-center justify-between">
            <span class="text-xs text-gray-400">{{ count($rekap) }} murid ditemukan</span>
            <span class="text-xs text-gray-400">
                Semester {{ $semester }} · Tahun Ajaran {{ $tahunAjaran }}
            </span>
        </div>
        @endif
    </div>

</div>

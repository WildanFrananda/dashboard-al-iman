<div class="flex flex-col gap-6 h-full">

    @php
        $summary = $this->summary;
        $student = $this->studentInfo;
        $initials = collect(explode(' ', trim((string) ($student['nama'] ?? ''))))
            ->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    @endphp

    <!-- FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 shrink-0">
        <div class="flex flex-col md:flex-row md:items-end gap-4">

            <!-- Semester -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/70 text-xs font-medium uppercase tracking-wide">Semester</label>
                <select wire:model.live="semester"
                    class="min-w-[160px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
                    <option value="1">Semester Ganjil</option>
                    <option value="2">Semester Genap</option>
                </select>
            </div>

            <!-- Tahun Ajaran -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/70 text-xs font-medium uppercase tracking-wide">Tahun Ajaran</label>
                <input type="text" wire:model.live.debounce.600ms="tahunAjaran" placeholder="2025/2026"
                    class="w-full md:w-[140px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
            </div>

            <div class="hidden md:block flex-1"></div>

            <!-- Unduh Raport -->
            <a href="{{ route('raport', ['semester' => $semester, 'tahun' => $tahunAjaran]) }}" target="_blank" rel="noopener"
                class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-[#F59E0B] hover:bg-[#D97706] text-white font-semibold text-sm px-6 py-2.5 rounded-md shadow-sm transition-colors">
                Unduh Raport
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                </svg>
            </a>
        </div>
    </div>

    <!-- IDENTITAS MURID + RINGKASAN -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 shrink-0">

        <!-- Kartu Identitas -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-[#0F609B] text-white flex items-center justify-center text-lg font-bold shrink-0">
                {{ $initials !== '' ? $initials : 'NA' }}
            </div>
            <div class="min-w-0">
                <p class="text-lg font-bold text-gray-900 truncate">{{ $student['nama'] ?? '—' }}</p>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $student['kelas'] ?? 'Kelas —' }}
                    <span class="text-gray-300">·</span>
                    NISN: {{ $student['nis'] ?? '—' }}
                </p>
            </div>
        </div>

        <!-- Rata-rata UTS -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-blue-100 p-3 rounded-full shrink-0 text-blue-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Rata-rata UTS</p>
                <p class="text-2xl font-bold text-gray-900">{{ $summary['rata_uts'] !== null ? $summary['rata_uts'] : '—' }}</p>
            </div>
        </div>

        <!-- Rata-rata UAS -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-purple-100 p-3 rounded-full shrink-0 text-purple-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wide">Rata-rata UAS</p>
                <p class="text-2xl font-bold text-gray-900">{{ $summary['rata_uas'] !== null ? $summary['rata_uas'] : '—' }}</p>
            </div>
        </div>

        <!-- Mata Pelajaran -->
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
        <div class="flex-1 overflow-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-[#F1F5F9] text-xs font-bold text-gray-600 uppercase tracking-wide">
                        <th class="text-left px-6 py-3.5 font-bold">Mata Pelajaran</th>
                        <th class="text-center px-3 py-3.5 font-bold">KKM</th>
                        <th class="text-center px-3 py-3.5 font-bold">Kehadiran</th>
                        <th class="text-center px-3 py-3.5 font-bold text-blue-700">UTS</th>
                        <th class="text-center px-3 py-3.5 font-bold text-purple-700">UAS</th>
                        <th class="text-center px-3 py-3.5 font-bold text-[#0F609B] bg-blue-50/70">Nilai Akhir<br>Rapor</th>
                        <th class="text-left px-6 py-3.5 font-bold">Capaian Kompetensi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->gradeRecords as $record)
                        <tr class="border-b border-dashed border-gray-200 hover:bg-gray-50/50 transition-colors">

                            <!-- Mata Pelajaran -->
                            <td class="px-6 py-4 font-medium text-gray-800 align-middle">{{ $record['subject'] }}</td>

                            <!-- KKM -->
                            <td class="px-3 py-4 text-center text-gray-600 align-middle">{{ $record['kkm'] }}</td>

                            <!-- Kehadiran -->
                            <td class="px-3 py-4 text-center align-middle">
                                @if($record['kehadiran'] !== null)
                                    <span class="font-medium {{ $record['kehadiran'] >= 75 ? 'text-green-600' : 'text-amber-600' }}">{{ $record['kehadiran'] }}%</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>

                            <!-- UTS -->
                            <td class="px-3 py-4 text-center align-middle">
                                @if($record['uts'] !== null)
                                    <span class="font-semibold {{ $record['uts'] >= $record['kkm'] ? 'text-gray-800' : 'text-red-600' }}">{{ $record['uts'] }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>

                            <!-- UAS -->
                            <td class="px-3 py-4 text-center align-middle">
                                @if($record['uas'] !== null)
                                    <span class="font-semibold {{ $record['uas'] >= $record['kkm'] ? 'text-gray-800' : 'text-red-600' }}">{{ $record['uas'] }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>

                            <!-- Nilai Akhir Rapor -->
                            <td class="px-3 py-4 text-center align-middle bg-blue-50/70">
                                @if($record['nilai_akhir'] !== null)
                                    <span class="text-base font-bold {{ $record['nilai_akhir'] >= $record['kkm'] ? 'text-[#0F609B]' : 'text-red-600' }}">{{ $record['nilai_akhir'] }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>

                            <!-- Capaian Kompetensi -->
                            <td class="px-6 py-4 text-xs text-gray-500 align-middle">{{ $record['keterangan'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center text-sm text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Belum ada nilai untuk semester dan tahun ajaran ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

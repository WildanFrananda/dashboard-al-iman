<div class="flex flex-col gap-6 h-full">

    <!-- FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col gap-4 shrink-0">

        <!-- Row 1: Nama Guru + Jadwal + Semester + Tahun Ajaran -->
        <div class="flex flex-col md:flex-row items-end gap-4">

            <!-- Nama Guru (readonly) -->
            <div class="w-full md:flex-1 flex flex-col gap-1">
                <label class="text-white/70 text-xs font-medium uppercase tracking-wide">Pengajar</label>
                <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-md px-4 py-2.5">
                    <svg class="w-4 h-4 text-white/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-white text-sm font-medium truncate">{{ $teacherName }}</span>
                </div>
            </div>

            <!-- Pilih Jadwal -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/70 text-xs font-medium uppercase tracking-wide">Kelas & Mata Pelajaran</label>
                <select wire:model.live="scheduleId"
                    class="min-w-[230px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
                    <option value="">Pilih Jadwal Mengajar...</option>
                    @foreach($this->schedules as $schedule)
                        <option value="{{ $schedule->id }}">
                            {{ $schedule->kelas->nama_kelas ?? '-' }} — {{ $schedule->subject->subject_name ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Semester -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/70 text-xs font-medium uppercase tracking-wide">Semester</label>
                <select wire:model.live="semester"
                    class="min-w-[130px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
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

            <!-- Submit -->
            <div class="w-full md:w-auto flex flex-col gap-1">
                <label class="text-white/70 text-xs font-medium uppercase tracking-wide opacity-0 select-none">Aksi</label>
                <button wire:click="submit" wire:loading.attr="disabled"
                    class="bg-[#F28B2B] hover:bg-[#D97706] disabled:opacity-60 text-white font-semibold py-2.5 px-8 rounded-md transition-colors flex items-center justify-center gap-2 min-w-[120px]">
                    <span wire:loading.remove wire:target="submit">Simpan Nilai</span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- TABEL NILAI -->
    <div class="bg-white rounded-[20px] shadow-sm overflow-hidden flex-1 border border-gray-100 flex flex-col">

        <!-- Table Header -->
        <div class="bg-[#F1F5F9] px-6 py-3.5 shrink-0 border-b border-gray-200">
            <div class="grid grid-cols-12 gap-3 text-xs font-bold text-gray-600 uppercase tracking-wide">
                <div class="col-span-1 text-center">#</div>
                <div class="col-span-1">NIS</div>
                <div class="col-span-4">Nama Murid</div>
                <div class="col-span-2 text-center text-blue-700">UTS <span class="font-normal">(0–100)</span></div>
                <div class="col-span-2 text-center text-purple-700">UAS <span class="font-normal">(0–100)</span></div>
                <div class="col-span-2 text-center text-gray-500">Keterangan</div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto">

            @if(empty($students))
                <div class="px-6 py-14 text-center text-sm text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Pilih jadwal, semester, dan tahun ajaran untuk mulai menginput nilai.
                </div>
            @endif

            @foreach($students as $i => $student)
                @php $muridId = $student['id']; @endphp
                <div wire:key="grade-{{ $muridId }}"
                    class="px-6 py-3 grid grid-cols-12 gap-3 items-center hover:bg-gray-50/60 transition-colors">

                    <!-- No -->
                    <div class="col-span-1 text-center text-xs text-gray-400 font-medium">{{ $i + 1 }}</div>

                    <!-- NIS -->
                    <div class="col-span-1 text-xs text-gray-500 font-mono">{{ $student['nis'] }}</div>

                    <!-- Nama -->
                    <div class="col-span-4 text-sm font-medium text-gray-800 truncate">{{ $student['name'] }}</div>

                    <!-- UTS Input -->
                    <div class="col-span-2 flex flex-col items-center gap-0.5"
                         x-data="gradeInput()">
                        <input
                            type="number"
                            wire:model.blur="grades.{{ $muridId }}.uts"
                            placeholder="—"
                            x-on:input="validate($el)"
                            :class="(invalid || @js($errors->has("grades.{$muridId}.uts")))
                                ? 'border-red-400 bg-red-50 ring-2 ring-red-200 focus:ring-red-300 focus:border-red-400'
                                : 'border-blue-200 bg-blue-50/40 focus:ring-blue-300 focus:border-blue-400'"
                            class="w-20 text-center text-sm rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 text-gray-800 placeholder-gray-300 transition-all">
                        {{-- Error dari server (blur / submit) --}}
                        @error("grades.{$muridId}.uts")
                            <span class="text-red-500 text-[10px] font-semibold leading-none whitespace-nowrap">
                                {{ $message }}
                            </span>
                        @enderror
                        {{-- Error dari Alpine (real-time saat mengetik) --}}
                        <span x-show="invalid && !@js($errors->has("grades.{$muridId}.uts"))" x-cloak
                              class="text-red-500 text-[10px] font-semibold leading-none">
                            0 – 100
                        </span>
                    </div>

                    <!-- UAS Input -->
                    <div class="col-span-2 flex flex-col items-center gap-0.5"
                         x-data="gradeInput()">
                        <input
                            type="number"
                            wire:model.blur="grades.{{ $muridId }}.uas"
                            placeholder="—"
                            x-on:input="validate($el)"
                            :class="(invalid || @js($errors->has("grades.{$muridId}.uas")))
                                ? 'border-red-400 bg-red-50 ring-2 ring-red-200 focus:ring-red-300 focus:border-red-400'
                                : 'border-purple-200 bg-purple-50/40 focus:ring-purple-300 focus:border-purple-400'"
                            class="w-20 text-center text-sm rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 text-gray-800 placeholder-gray-300 transition-all">
                        @error("grades.{$muridId}.uas")
                            <span class="text-red-500 text-[10px] font-semibold leading-none whitespace-nowrap">
                                {{ $message }}
                            </span>
                        @enderror
                        <span x-show="invalid && !@js($errors->has("grades.{$muridId}.uas"))" x-cloak
                              class="text-red-500 text-[10px] font-semibold leading-none">
                            0 – 100
                        </span>
                    </div>

                    <!-- Keterangan -->
                    <div class="col-span-2">
                        <input
                            type="text"
                            wire:model.blur="grades.{{ $muridId }}.keterangan"
                            placeholder="opsional"
                            maxlength="200"
                            class="w-full text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-gray-300 text-gray-600 placeholder-gray-300 transition-colors">
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Footer -->
        @if(count($students) > 0)
        <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-100 shrink-0 flex items-center justify-between">
            <span class="text-xs text-gray-400">{{ count($students) }} murid</span>
            <span class="text-xs text-gray-400">Nilai diisi otomatis tersimpan saat klik di luar field (blur).</span>
        </div>
        @endif
    </div>

    <!-- Validation Error Banner (muncul saat submit dengan nilai invalid) -->
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg shrink-0 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-700">Terdapat nilai yang tidak valid — data belum disimpan.</p>
                <p class="text-xs text-red-600 mt-0.5">Periksa field yang ditandai merah. Nilai harus berupa angka antara <strong>0 – 100</strong>.</p>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div class="p-4 text-green-700 bg-green-100 rounded-lg border border-green-200 shrink-0 flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="p-4 text-red-700 bg-red-100 rounded-lg border border-red-200 shrink-0">
            {{ session('error') }}
        </div>
    @endif

</div>

@script
<script>
    Alpine.data('gradeInput', () => ({
        invalid: false,

        validate(el) {
            const val = el.value;
            if (val === '') {
                this.invalid = false;
                return;
            }
            const n = parseInt(val, 10);
            // Tandai invalid jika di luar range — TIDAK auto-koreksi
            // agar Livewire menerima nilai asli dan bisa tampilkan error
            this.invalid = isNaN(n) || n < 0 || n > 100;
        },
    }));
</script>
@endscript

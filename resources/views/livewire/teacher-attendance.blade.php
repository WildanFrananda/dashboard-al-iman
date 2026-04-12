<div class="flex flex-col gap-6 h-full">

    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col gap-4 shrink-0">

        <!-- Row 1: Teacher Name + Schedule + Date -->
        <div class="flex flex-col md:flex-row items-center gap-4">

            <!-- Teacher Name -->
            <div class="w-full md:flex-1 flex items-center gap-3">
                <div class="text-white shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <input type="text" wire:model="teacherName" readonly
                    class="w-full bg-white/10 border border-white/20 rounded-md px-4 py-2.5 text-sm text-white placeholder-white/60 cursor-not-allowed"
                    placeholder="Nama Pengajar">
            </div>

            <!-- Class / Schedule Select -->
            <div class="w-full md:w-auto flex items-center gap-3">
                <div class="text-white shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 19v-2M4 17V5a2 2 0 012-2h12a2 2 0 012 2v12M4 17h16M14 10v.01" />
                    </svg>
                </div>
                <select wire:model.live="scheduleId"
                    class="w-full md:w-auto min-w-[220px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
                    <option value="">Pilih Jadwal Mengajar..</option>
                    @foreach ($this->schedules as $schedule)
                        <option value="{{ $schedule->id }}">
                            {{ $schedule->kelas->nama_kelas ?? 'Tanpa Kelas' }} -
                            {{ $schedule->subject->subject_name ?? 'Tanpa Mapel' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Picker -->
            <div class="w-full md:w-auto flex items-center gap-3">
                <div class="text-white shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <input
                    id="datepicker"
                    type="date"
                    wire:model.live="date"
                    class="w-full md:w-[160px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
            </div>

            <!-- Submit -->
            <button wire:click="submit" wire:loading.attr="disabled"
                class="w-full md:w-auto bg-[#F28B2B] hover:bg-[#D97706] disabled:opacity-60 text-white font-medium py-2.5 px-8 rounded-md transition-colors shrink-0 flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="submit">Submit</span>
                <span wire:loading wire:target="submit">Menyimpan...</span>
            </button>
        </div>

        <!-- Row 2: Materi Pembelajaran -->
        <div class="flex items-center gap-3 w-full">
            <div class="text-white shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.defer="materi"
                maxlength="500"
                placeholder="Materi Pembelajaran (opsional, contoh: Perkalian Pecahan Desimal)"
                class="w-full bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-white">
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="bg-white rounded-[20px] shadow-sm overflow-hidden flex-1 border border-gray-100 flex flex-col">

        <!-- Table Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0">
            <div class="font-bold text-gray-800 text-sm">Nama Murid</div>
            <div class="font-bold text-gray-800 text-sm w-[380px] pl-2 md:pl-0">Status Kehadiran</div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-dashed divide-gray-200 border-t border-gray-200 flex-1 overflow-y-auto">

            @if (empty($students))
                <div class="px-6 py-10 text-center text-sm text-gray-400">
                    Pilih jadwal dan tanggal untuk menampilkan daftar murid.
                </div>
            @endif

            @foreach ($students as $student)
                <div wire:key="student-{{ $student['id'] }}"
                    class="px-6 py-3.5 flex flex-col md:flex-row md:justify-between md:items-center hover:bg-gray-50/50 transition-colors gap-3 md:gap-0">

                    <!-- Nama -->
                    <div class="font-normal text-gray-800 text-sm">
                        {{ $student['name'] }}
                    </div>

                    <!-- Status Buttons -->
                    <div class="flex items-center gap-2 w-full md:w-[380px] flex-wrap md:flex-nowrap">

                        {{-- HADIR --}}
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="attendances.{{ $student['id'] }}" value="Hadir"
                                name="absensi_{{ $student['id'] }}" class="hidden peer">
                            <div class="text-center py-1.5 px-3 rounded-md text-xs font-semibold border transition-all duration-150
                                        bg-white border-emerald-200 text-emerald-600 opacity-50 hover:opacity-80 hover:bg-emerald-50
                                        peer-checked:opacity-100 peer-checked:bg-emerald-50 peer-checked:border-emerald-400 peer-checked:text-emerald-800 peer-checked:ring-1 peer-checked:ring-emerald-300">
                                Hadir
                            </div>
                        </label>

                        {{-- IZIN --}}
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="attendances.{{ $student['id'] }}" value="Izin"
                                name="absensi_{{ $student['id'] }}" class="hidden peer">
                            <div class="text-center py-1.5 px-3 rounded-md text-xs font-semibold border transition-all duration-150
                                        bg-white border-amber-200 text-amber-600 opacity-50 hover:opacity-80 hover:bg-amber-50
                                        peer-checked:opacity-100 peer-checked:bg-amber-50 peer-checked:border-amber-400 peer-checked:text-amber-800 peer-checked:ring-1 peer-checked:ring-amber-300">
                                Izin
                            </div>
                        </label>

                        {{-- SAKIT --}}
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="attendances.{{ $student['id'] }}" value="Sakit"
                                name="absensi_{{ $student['id'] }}" class="hidden peer">
                            <div class="text-center py-1.5 px-3 rounded-md text-xs font-semibold border transition-all duration-150
                                        bg-white border-blue-200 text-blue-600 opacity-50 hover:opacity-80 hover:bg-blue-50
                                        peer-checked:opacity-100 peer-checked:bg-blue-50 peer-checked:border-blue-400 peer-checked:text-blue-800 peer-checked:ring-1 peer-checked:ring-blue-300">
                                Sakit
                            </div>
                        </label>

                        {{-- ALPA --}}
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="attendances.{{ $student['id'] }}" value="Alpa"
                                name="absensi_{{ $student['id'] }}" class="hidden peer">
                            <div class="text-center py-1.5 px-3 rounded-md text-xs font-semibold border transition-all duration-150
                                        bg-white border-red-200 text-red-600 opacity-50 hover:opacity-80 hover:bg-red-50
                                        peer-checked:opacity-100 peer-checked:bg-red-50 peer-checked:border-red-400 peer-checked:text-red-800 peer-checked:ring-1 peer-checked:ring-red-300">
                                Alpa
                            </div>
                        </label>

                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Feedback -->
    @if (session()->has('message'))
        <div class="p-4 text-green-700 bg-green-100 rounded-lg border border-green-200 shrink-0">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 text-red-700 bg-red-100 rounded-lg border border-red-200 shrink-0">
            {{ session('error') }}
        </div>
    @endif

</div>

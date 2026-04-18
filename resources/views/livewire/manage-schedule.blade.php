<div class="flex flex-col gap-6 h-full">

    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0 justify-between">

        <div class="text-white font-bold text-lg hidden md:block">Manage Jadwal Mengajar</div>

        <!-- Search -->
        <div class="w-full md:w-[300px] relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                </svg>
            </span>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari jadwal (Guru/Mapel/Kelas)..."
                   class="w-full pl-9 pr-4 py-2 rounded-full bg-white text-gray-800 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/50">
        </div>

        <!-- Tambah -->
        <button type="button" wire:click="openForm"
                class="w-full md:w-auto flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#F28B2B] hover:bg-[#D97706] text-white text-sm font-semibold transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah Jadwal
        </button>
    </div>

    <!-- FLASH MESSAGE -->
    @if(session('message'))
    <div class="flex items-center gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('message') }}
    </div>
    @endif

    <!-- TABLE -->
    <div class="relative bg-white rounded-[20px] shadow-sm flex-1 border border-gray-100 flex flex-col overflow-hidden min-h-[400px]">

        <!-- Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex items-center shrink-0 border-b border-gray-100 gap-3">
            <div class="font-bold text-gray-800 text-sm w-[110px]">Hari</div>
            <div class="font-bold text-gray-800 text-sm w-[120px]">Jam</div>
            <div class="font-bold text-gray-800 text-sm flex-1">Mata Pelajaran</div>
            <div class="font-bold text-gray-800 text-sm w-[180px] hidden md:block">Guru</div>
            <div class="font-bold text-gray-800 text-sm w-[110px] hidden sm:block">Kelas</div>
            <div class="font-bold text-gray-800 text-sm w-[90px] text-center">Aksi</div>
        </div>

        <!-- Body -->
        <div wire:loading.class="opacity-50" class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto w-full">
            @forelse($schedules as $item)
            <div wire:key="schedule-{{ $item->id }}"
                 class="px-6 py-4 flex items-center hover:bg-gray-50/50 transition-colors gap-3">

                <!-- Hari -->
                <div class="w-[110px]">
                    @php
                        $hariColor = match($item->hari) {
                            'Senin'  => 'bg-blue-100 text-blue-700',
                            'Selasa' => 'bg-green-100 text-green-700',
                            'Rabu'   => 'bg-purple-100 text-purple-700',
                            'Kamis'  => 'bg-orange-100 text-orange-700',
                            'Jumat'  => 'bg-emerald-100 text-emerald-700',
                            default  => 'bg-rose-100 text-rose-700',
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $hariColor }}">
                        {{ $item->hari }}
                    </span>
                </div>

                <!-- Jam -->
                <div class="text-gray-600 text-sm w-[120px] font-mono">
                    {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                    –
                    {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                </div>

                <!-- Mata Pelajaran -->
                <div class="font-medium text-gray-800 text-sm flex-1 truncate">
                    {{ $item->subject?->subject_name ?? '-' }}
                </div>

                <!-- Guru -->
                <div class="text-gray-500 text-sm w-[180px] hidden md:block truncate">
                    {{ $item->guru?->nama_lengkap ?? '-' }}
                </div>

                <!-- Kelas -->
                <div class="w-[110px] hidden sm:block">
                    <span class="bg-gray-100 px-2 py-1 rounded text-xs">
                        {{ $item->kelas?->nama_kelas ?? '-' }}
                    </span>
                </div>

                <!-- Aksi -->
                <div class="w-[90px] flex items-center justify-center gap-2">
                    <button type="button" wire:click="edit({{ $item->id }})"
                            class="p-1.5 rounded-md text-[#0F609B] hover:bg-blue-50 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                        </svg>
                    </button>
                    <button type="button"
                            wire:click="delete({{ $item->id }})"
                            wire:confirm="Hapus jadwal {{ $item->subject?->subject_name }} – {{ $item->hari }}?"
                            class="p-1.5 rounded-md text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                        </svg>
                    </button>
                </div>

            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-gray-500 h-64 text-sm">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                Belum ada data jadwal yang ditemukan.
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($schedules->hasPages())
        <div class="p-4 border-t border-gray-100 shrink-0 bg-white rounded-b-[20px]">
            {{ $schedules->links() }}
        </div>
        @endif
    </div>

    <!-- ── MODAL TAMBAH / EDIT ────────────────────────────────────────────────── -->
    <div x-data
         x-show="$wire.showForm"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-cloak
         @keydown.escape.window="$wire.closeForm()">

        <div x-show="$wire.showForm"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg"
             @click.stop>

            <!-- Header -->
            <div class="bg-[#0F609B] rounded-t-2xl px-6 py-4 flex items-center justify-between">
                <h2 class="text-white font-bold text-base">
                    {{ $editingId ? 'Edit Jadwal Mengajar' : 'Tambah Jadwal Baru' }}
                </h2>
                <button type="button" @click="$wire.closeForm()" class="text-white/70 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                <!-- Guru -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Guru <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="guru_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                   {{ $errors->has('guru_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_lengkap }} ({{ $g->nip }})</option>
                        @endforeach
                    </select>
                    @error('guru_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="subject_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                   {{ $errors->has('subject_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->subject_name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="kelas_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                   {{ $errors->has('kelas_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->nama_kelas }} ({{ $c->tahun_ajaran }})</option>
                        @endforeach
                    </select>
                    @error('kelas_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Hari -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Hari <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="hari"
                            class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                   {{ $errors->has('hari') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        <option value="">-- Pilih Hari --</option>
                        @foreach($days as $day)
                            <option value="{{ $day }}">{{ $day }}</option>
                        @endforeach
                    </select>
                    @error('hari') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jam Mulai & Selesai -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jam Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" wire:model="jam_mulai"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                      {{ $errors->has('jam_mulai') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        @error('jam_mulai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jam Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" wire:model="jam_selesai"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                      {{ $errors->has('jam_selesai') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        @error('jam_selesai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="px-6 pb-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                <button type="button" @click="$wire.closeForm()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="button" wire:click="save" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-[#0F609B] hover:bg-[#0d5289] rounded-lg transition-colors disabled:opacity-50">
                    <svg wire:loading wire:target="save" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Jadwal' }}
                    </span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>

        </div>
    </div>

</div>

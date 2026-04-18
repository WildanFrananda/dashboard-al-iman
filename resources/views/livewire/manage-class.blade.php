<div class="flex flex-col gap-6 h-full p-6">

    <!-- Flash Messages -->
    @if(session('message'))
        <div class="flex items-center gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0 justify-between">
        <div class="text-white font-bold text-lg hidden md:block">
            Manage Kelas & Kurikulum
        </div>

        <div class="w-full md:w-[300px]">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari kelas..." class="w-full [&_input]:!rounded-full [&_input]:!bg-white [&_input]:!text-gray-800 [&_input::placeholder]:!text-gray-400 [&_svg]:!text-gray-400" />
        </div>

        <div class="flex gap-2 w-full md:w-auto">
            <button wire:click="openPromotion"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 hover:bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors flex-1 md:flex-none justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
                </svg>
                Kenaikan Kelas
            </button>
            <button wire:click="openForm"
                class="inline-flex items-center gap-2 rounded-lg bg-[#F28B2B] hover:bg-[#D97706] px-4 py-2.5 text-sm font-semibold text-white transition-colors flex-1 md:flex-none justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Kelas
            </button>
        </div>
    </div>

    <!-- MAIN CONTENT AREA (TABLE) -->
    <div class="relative bg-white rounded-[20px] shadow-sm flex-1 border border-gray-100 flex flex-col overflow-hidden min-h-[400px]">

        <!-- Table Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0 border-b border-gray-100">
            <div class="font-bold text-gray-800 text-sm w-[150px] hidden sm:block">Kode Kelas</div>
            <div class="font-bold text-gray-800 text-sm flex-1">Nama Kelas</div>
            <div class="font-bold text-gray-800 text-sm w-[200px] hidden md:block">Wali Kelas</div>
            <div class="font-bold text-gray-800 text-sm w-full sm:w-[100px] text-right sm:text-center">Aksi</div>
        </div>

        <!-- Table Body -->
        <div wire:loading.class="opacity-50" class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto w-full">
            @forelse($classes as $item)
            <div wire:key="class-{{ $item->id }}" class="px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center hover:bg-gray-50/50 transition-colors gap-3 sm:gap-0">

                <div class="text-gray-500 font-mono text-sm w-full sm:w-[150px] hidden sm:block">
                    {{ $item->kode_kelas }}
                </div>

                <div class="font-medium text-gray-800 text-sm flex-1 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-md bg-green-100 text-green-600 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $item->level ?? '?' }}
                    </div>
                    <span>{{ $item->nama_kelas }}</span>
                </div>

                <div class="text-gray-500 text-sm w-[200px] hidden md:block">
                    {{ $item->waliKelas?->nama_lengkap ?? '-' }}
                </div>

                <div class="w-full sm:w-[100px] flex items-center justify-end sm:justify-center gap-2">
                    <button wire:click="edit({{ $item->id }})"
                        class="p-1.5 rounded-md text-[#0F609B] hover:bg-blue-50 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button wire:click="confirmDelete({{ $item->id }})"
                        class="p-1.5 rounded-md text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-gray-500 h-64 text-sm">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.75M5.25 9h3.75m-3.75 3h3.75m-3.75 3h3.75m3-8.25h3.75m-3.75 3h3.75m-3.75 3h3.75M9 21v-8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 21h18" />
                </svg>
                Belum ada data kelas yang ditemukan.
            </div>
            @endforelse
        </div>

        @if($classes->hasPages())
        <div class="p-4 border-t border-gray-100 shrink-0 bg-white rounded-b-[20px]">
            {{ $classes->links() }}
        </div>
        @endif
    </div>

    <!-- ── MODAL: TAMBAH / EDIT KELAS ──────────────────────────────────────── -->
    <div
        x-data
        x-show="$wire.showForm"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        x-cloak
        @keydown.escape.window="$wire.closeForm()"
    >
        <div
            x-show="$wire.showForm"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"  x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl w-full max-w-md"
            @click.stop
        >
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900 text-base">{{ $editingId ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h2>
                <button wire:click="closeForm" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Kelas</label>
                    <input type="text" wire:model="kode_kelas" placeholder="Contoh: SD-1A"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] {{ $errors->has('kode_kelas') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error('kode_kelas') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kelas</label>
                    <input type="text" wire:model="nama_kelas" placeholder="Contoh: Kelas 1A"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] {{ $errors->has('nama_kelas') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error('nama_kelas') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun Ajaran</label>
                    <input type="text" wire:model="tahun_ajaran" placeholder="Contoh: 2024/2025"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] {{ $errors->has('tahun_ajaran') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error('tahun_ajaran') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Wali Kelas</label>
                    <select wire:model="wali_kelas_id"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] {{ $errors->has('wali_kelas_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    @error('wali_kelas_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="closeForm"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-[#0F609B] hover:bg-[#0d5289] transition-colors">
                        <svg wire:loading.remove class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                        </svg>
                        <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <span wire:loading.remove>{{ $editingId ? 'Simpan Perubahan' : 'Simpan Kelas' }}</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── MODAL: KENAIKAN KELAS ───────────────────────────────────────────── -->
    <div
        x-data
        x-show="$wire.showPromotionModal"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        x-cloak
        @keydown.escape.window="$wire.set('showPromotionModal', false)"
    >
        <div
            x-show="$wire.showPromotionModal"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"  x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl w-full max-w-lg"
            @click.stop
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-green-100">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-900 text-base">Kenaikan Kelas Otomatis</h2>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Langkah {{ $promotionStep }} dari 2
                        </p>
                    </div>
                </div>
                <button wire:click="$set('showPromotionModal', false)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Step indicator -->
            <div class="px-6 pt-4">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 flex-1">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                            {{ $promotionStep >= 1 ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">1</div>
                        <span class="text-xs font-medium {{ $promotionStep >= 1 ? 'text-green-700' : 'text-gray-400' }}">Tahun Ajaran</span>
                    </div>
                    <div class="h-px flex-1 {{ $promotionStep >= 2 ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                    <div class="flex items-center gap-2 flex-1 justify-end">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                            {{ $promotionStep >= 2 ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">2</div>
                        <span class="text-xs font-medium {{ $promotionStep >= 2 ? 'text-green-700' : 'text-gray-400' }}">Tinggal Kelas</span>
                    </div>
                </div>
            </div>

            <!-- Step 1: Tahun Ajaran -->
            @if($promotionStep === 1)
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">
                    Sistem akan menaikkan semua murid aktif ke tingkat berikutnya. Murid kelas 6 akan otomatis berstatus <span class="font-semibold text-green-700">Lulus</span>.
                </p>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 space-y-3">
                    <label class="block text-sm font-medium text-blue-800">Tahun Ajaran Baru</label>
                    <input type="text" wire:model="newAcademicYear" placeholder="Contoh: 2025/2026"
                        class="w-full rounded-lg border border-blue-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400">
                    <p class="text-xs text-blue-500">Semua murid aktif akan dipindahkan ke tahun ajaran ini.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="$set('showPromotionModal', false)"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="startPromotion"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-green-600 hover:bg-green-700 transition-colors">
                        Lanjut
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>
            </div>
            @endif

            <!-- Step 2: Pilih Tinggal Kelas -->
            @if($promotionStep === 2)
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">
                    Centang murid yang <span class="font-semibold text-red-600">tidak naik kelas</span>. Murid yang tidak dicentang akan otomatis naik tingkat.
                </p>

                <!-- Student list -->
                <div class="border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100 max-h-[280px] overflow-y-auto">
                    @forelse($activeStudents as $student)
                    <label wire:key="student-{{ $student->id }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox"
                            wire:model="selectedStayBackIds"
                            value="{{ $student->id }}"
                            class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-400 shrink-0">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <div class="h-7 w-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr($student->nama_lengkap, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $student->nama_lengkap }}</p>
                                <p class="text-xs text-gray-400 truncate">
                                    {{ $student->kelas()->wherePivot('tahun_ajaran', $tahun_ajaran)->first()?->nama_kelas ?? 'Tanpa Kelas' }}
                                </p>
                            </div>
                        </div>
                        @if(in_array((string)$student->id, (array)$selectedStayBackIds))
                        <span class="shrink-0 text-xs font-semibold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">Tinggal Kelas</span>
                        @endif
                    </label>
                    @empty
                    <div class="px-4 py-6 text-center text-sm text-gray-400">Tidak ada murid aktif.</div>
                    @endforelse
                </div>

                @if(count((array)$selectedStayBackIds) > 0)
                <div class="flex items-center gap-2 text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 text-xs">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <span><strong>{{ count((array)$selectedStayBackIds) }} murid</strong> akan tetap di kelas lamanya di tahun ajaran <strong>{{ $newAcademicYear }}</strong>.</span>
                </div>
                @endif

                <div class="flex justify-between gap-3 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="$set('promotionStep', 1)"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali
                    </button>
                    <button type="button" wire:click="processPromotion"
                        wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-[#0F609B] hover:bg-[#0d5289] transition-colors">
                        <svg wire:loading.remove class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
                        </svg>
                        <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <span wire:loading.remove>Proses Kenaikan Kelas</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- ── MODAL: KONFIRMASI HAPUS ─────────────────────────────────────────── -->
    <div
        x-data
        x-show="$wire.confirmingDelete !== null"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        x-cloak
    >
        <div
            x-show="$wire.confirmingDelete !== null"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"  x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl w-full max-w-sm"
            @click.stop
        >
            <div class="p-6 text-center space-y-4">
                <div class="mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Hapus Kelas?</h3>
                    <p class="text-sm text-gray-500 mt-1">Tindakan ini tidak dapat dibatalkan dan akan menghapus data kelas secara permanen.</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('confirmingDelete', null)"
                        class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="delete({{ $confirmingDelete ?? 0 }})"
                        wire:loading.attr="disabled"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

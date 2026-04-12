<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan Sekolah</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi statistik yang ditampilkan di dashboard.</p>
        </div>
    </div>

    <!-- Flash Message -->
    @if(session('settings_message'))
        <div class="flex items-center gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('settings_message') }}
        </div>
    @endif

    <!-- Settings Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">

        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Statistik Dashboard</h2>
            <p class="text-xs text-gray-400 mt-0.5">Nilai yang tersimpan di sini akan muncul di kartu statistik pada halaman Dashboard.</p>
        </div>

        <!-- Form Body -->
        <form wire:submit="save" class="p-6 space-y-6">

            <!-- Jumlah Ekstrakurikuler -->
            <div>
                <label for="ekskul" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Jumlah Ekstrakurikuler
                </label>
                <div class="relative w-full max-w-xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                    <input
                        id="ekskul"
                        type="number"
                        min="0"
                        max="999"
                        wire:model="jumlah_ekskul"
                        class="w-full rounded-lg border pl-9 pr-4 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                               {{ $errors->has('jumlah_ekskul') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                        placeholder="Contoh: 12"
                    >
                </div>
                @error('jumlah_ekskul')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-400">Masukkan jumlah kegiatan ekstrakurikuler yang aktif.</p>
            </div>

            <!-- Tingkat Akreditasi -->
            <div>
                <label for="akreditasi" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Tingkat Akreditasi
                </label>
                <div class="relative w-full max-w-xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <input
                        id="akreditasi"
                        type="text"
                        maxlength="10"
                        wire:model="tingkat_akreditasi"
                        class="w-full rounded-lg border pl-9 pr-4 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                               {{ $errors->has('tingkat_akreditasi') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                        placeholder="Contoh: A+"
                    >
                </div>
                @error('tingkat_akreditasi')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-400">Contoh: A, A+, B, dll. Maksimal 10 karakter.</p>
            </div>

            <!-- Submit -->
            <div class="pt-2 border-t border-gray-100">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#0F609B] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#0d5289] focus:outline-none focus:ring-2 focus:ring-[#0F609B]/40 transition-colors"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                >
                    <svg wire:loading.remove class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                    </svg>
                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    <span wire:loading.remove>Simpan Pengaturan</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>

        </form>
    </div>

</div>

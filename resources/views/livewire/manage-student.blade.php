<div class="flex flex-col gap-6 h-full">

    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0 justify-between">

        <div class="text-white font-bold text-lg hidden md:block">Manage Murid</div>

        <!-- Search -->
        <div class="w-full md:w-[300px] relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                </svg>
            </span>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari murid..."
                   class="w-full pl-9 pr-4 py-2 rounded-full bg-white text-gray-800 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/50">
        </div>

        <!-- Year Selector -->
        <div class="w-full md:w-[160px]">
            <select wire:model.live="selectedYear"
                    class="w-full px-4 py-2 rounded-full bg-white text-gray-700 text-sm border-0 focus:outline-none focus:ring-2 focus:ring-white/50 cursor-pointer">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <!-- Tambah -->
        <button type="button" wire:click="openForm"
                class="w-full md:w-auto flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#F28B2B] hover:bg-[#D97706] text-white text-sm font-semibold transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Daftarkan Murid Baru
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
    @if(session('error'))
    <div class="flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    <!-- STATUS FILTER TABS -->
    <div class="flex flex-wrap gap-3 shrink-0">
        @foreach([
            'all'    => ['label' => 'Semua Murid',    'active' => 'bg-[#0F609B] text-white shadow-md',     'inactive' => ''],
            'aktif'  => ['label' => 'Aktif',           'active' => 'bg-green-600 text-white shadow-md',     'inactive' => ''],
            'lulus'  => ['label' => 'Lulus',           'active' => 'bg-blue-600 text-white shadow-md',      'inactive' => ''],
            'pindah' => ['label' => 'Pindah / Keluar', 'active' => 'bg-orange-600 text-white shadow-md',   'inactive' => ''],
        ] as $val => $cfg)
        <button type="button"
                wire:click="$set('selectedStatus', '{{ $val }}')"
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200
                       {{ $selectedStatus === $val
                            ? $cfg['active']
                            : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            {{ $cfg['label'] }}
        </button>
        @endforeach
    </div>

    <!-- TABLE -->
    <div class="relative bg-white rounded-[20px] shadow-sm flex-1 border border-gray-100 flex flex-col overflow-hidden min-h-[400px]">

        <!-- Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0 border-b border-gray-100">
            <div class="font-bold text-gray-800 text-sm w-[120px] hidden sm:block">NIS</div>
            <div class="font-bold text-gray-800 text-sm flex-1">Nama Murid</div>
            <div class="font-bold text-gray-800 text-sm w-[130px] hidden md:block">Kelas</div>
            <div class="font-bold text-gray-800 text-sm w-[100px] hidden sm:block text-center">Status</div>
            <div class="font-bold text-gray-800 text-sm w-[100px] text-center">Aksi</div>
        </div>

        <!-- Skeleton -->
        <div wire:loading class="w-full divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto">
            <flux:skeleton.group animate="shimmer">
                @for($i = 0; $i < 5; $i++)
                <div class="px-6 py-4 flex justify-between items-center gap-3">
                    <div class="w-[120px] hidden sm:block"><flux:skeleton class="w-20 h-5"/></div>
                    <div class="flex-1 flex flex-col gap-1.5">
                        <flux:skeleton class="w-40 h-5"/>
                        <flux:skeleton class="w-28 h-3"/>
                    </div>
                    <div class="w-[130px] hidden md:block"><flux:skeleton class="w-20 h-6 rounded"/></div>
                    <div class="w-[100px] hidden sm:flex justify-center"><flux:skeleton class="w-16 h-6 rounded-full"/></div>
                    <div class="w-[100px] flex justify-center gap-2">
                        <flux:skeleton class="w-8 h-8 rounded-md"/>
                        <flux:skeleton class="w-8 h-8 rounded-md"/>
                    </div>
                </div>
                @endfor
            </flux:skeleton.group>
        </div>

        <!-- Body -->
        <div wire:loading.class="hidden" class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto w-full">
            @forelse($students as $item)
            <div wire:key="student-{{ $item->id }}"
                 class="px-6 py-4 flex justify-between items-center hover:bg-gray-50/50 transition-colors gap-3">

                <!-- NIS -->
                <div class="text-gray-500 font-mono text-sm w-[120px] hidden sm:block">
                    {{ $item->nis }}
                </div>

                <!-- Nama -->
                <div class="font-medium text-gray-800 text-sm flex-1 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ mb_strtoupper(mb_substr($item->nama_lengkap, 0, 1)) }}
                    </div>
                    <div>
                        <p>{{ $item->nama_lengkap }}</p>
                        <p class="text-xs text-gray-400">{{ $item->user->email }}</p>
                        <!-- Mobile: NIS & status -->
                        <div class="sm:hidden flex items-center gap-2 mt-1">
                            <span class="font-mono text-xs text-gray-400">{{ $item->nis }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                {{ $item->status === 'aktif'  ? 'bg-green-100 text-green-700'  :
                                  ($item->status === 'lulus'  ? 'bg-blue-100 text-blue-700'    :
                                  ($item->status === 'pindah' ? 'bg-orange-100 text-orange-700' :
                                                                'bg-red-100 text-red-700')) }}">
                                {{ $item->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Kelas -->
                <div class="text-sm w-[130px] hidden md:block">
                    @php $k = $item->kelas->first(); @endphp
                    @if($k)
                        <span class="px-2 py-1 rounded bg-gray-100 text-xs font-medium">{{ $k->nama_kelas }}</span>
                    @elseif($item->status === 'lulus')
                        <span class="text-gray-400 text-xs italic">Alumni</span>
                    @else
                        <span class="text-gray-400 text-xs">Belum assign</span>
                    @endif
                </div>

                <!-- Status -->
                <div class="w-[100px] hidden sm:flex justify-center shrink-0">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase
                        {{ $item->status === 'aktif'  ? 'bg-green-100 text-green-700'  :
                          ($item->status === 'lulus'  ? 'bg-blue-100 text-blue-700'    :
                          ($item->status === 'pindah' ? 'bg-orange-100 text-orange-700' :
                                                        'bg-red-100 text-red-700')) }}">
                        {{ $item->status }}
                    </span>
                </div>

                <!-- Aksi -->
                <div class="w-[100px] flex items-center justify-center gap-2 shrink-0">
                    <button type="button" wire:click="edit({{ $item->id }})"
                            class="p-1.5 rounded-md text-[#0F609B] hover:bg-blue-50 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                        </svg>
                    </button>
                    <button type="button"
                            wire:click="delete({{ $item->id }})"
                            wire:confirm="Hapus data murid '{{ $item->nama_lengkap }}'? Akun login-nya juga akan terhapus."
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
                Belum ada data murid yang ditemukan.
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="p-4 border-t border-gray-100 shrink-0 bg-white rounded-b-[20px]">
            {{ $students->links() }}
        </div>
        @endif

    </div>

    <!-- ── MODAL TAMBAH / EDIT ──────────────────────────────────────────────── -->
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
             class="bg-white rounded-2xl shadow-xl w-full max-w-xl"
             @click.stop>

            <!-- Header -->
            <div class="bg-[#0F609B] rounded-t-2xl px-6 py-4 flex items-center justify-between">
                <h2 class="text-white font-bold text-base">
                    {{ $editingId ? 'Edit Data Murid' : 'Daftarkan Murid Baru' }}
                </h2>
                <button type="button" @click="$wire.closeForm()" class="text-white/70 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- Nama Panggilan (User) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Panggilan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="name"
                               placeholder="Contoh: Budi"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                      {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email Login <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model="email"
                               placeholder="budi@email.com"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                      {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- NIS -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            NIS <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nis"
                               placeholder="Contoh: 20230001"
                               class="w-full border rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                      {{ $errors->has('nis') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        @error('nis') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                            @if($editingId)
                                <span class="text-xs font-normal text-gray-400 ml-1">(opsional)</span>
                            @else
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        <input type="password" wire:model="password"
                               placeholder="{{ $editingId ? 'Kosongkan jika tidak ganti' : 'Minimal 6 karakter' }}"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                      {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nama_lengkap"
                               placeholder="Nama lengkap sesuai akte"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                                      {{ $errors->has('nama_lengkap') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        @error('nama_lengkap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kelas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kelas {{ !$editingId ? '<span class="text-red-500">*</span>' : '' }}
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

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status Murid <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="status"
                                class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] border-gray-300">
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus</option>
                            <option value="pindah">Pindah</option>
                            <option value="keluar">Keluar (Dikeluarkan)</option>
                        </select>
                        @error('status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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
                        {{ $editingId ? 'Simpan Perubahan' : 'Daftarkan Murid' }}
                    </span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>

        </div>
    </div>

</div>

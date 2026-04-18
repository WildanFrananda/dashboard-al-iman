<div class="flex flex-col gap-6 h-full" x-data>

    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0 justify-between">
        <div class="text-white font-bold text-lg hidden md:block">Manage Pelajaran</div>

        <!-- Search -->
        <div class="w-full md:w-[300px]">
            <flux:input wire:model.live.debounce.300ms="search"
                        icon="magnifying-glass"
                        placeholder="Cari pelajaran (kode/nama)..."
                        class="w-full [&_input]:!rounded-full [&_input]:!bg-white [&_input]:!text-gray-800 [&_input::placeholder]:!text-gray-400 [&_svg]:!text-gray-400" />
        </div>

        <!-- Tambah -->
        <button type="button" wire:click="openAddForm"
                class="w-full md:w-auto flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-[#F28B2B] hover:bg-[#D97706] text-white text-sm font-semibold transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pelajaran
        </button>
    </div>

    <!-- FLASH MESSAGE -->
    @if(session('message'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-medium">{{ session('message') }}</span>
    </div>
    @endif

    <!-- TABLE -->
    <div class="flex-1 flex flex-col gap-4">
        <div class="relative bg-white rounded-[20px] shadow-sm flex-1 border border-gray-100 flex flex-col overflow-hidden min-h-[400px]">

            <!-- Table Header -->
            <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0 border-b border-gray-100">
                <div class="font-bold text-gray-800 text-sm w-[150px] hidden sm:block">Kode Mapel</div>
                <div class="font-bold text-gray-800 text-sm flex-1">Nama Pelajaran</div>
                <div class="font-bold text-gray-800 text-sm w-[150px] hidden lg:block">Kategori</div>
                <div class="font-bold text-gray-800 text-sm w-[100px] hidden sm:block text-center">Status</div>
                <div class="font-bold text-gray-800 text-sm w-[100px] text-center">Aksi</div>
            </div>

            <!-- Loading Skeleton -->
            <div wire:loading class="w-full divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto">
                <flux:skeleton.group animate="shimmer">
                    @for($i = 0; $i < 5; $i++)
                    <div class="px-6 py-4 flex sm:justify-between sm:items-center gap-3">
                        <div class="w-[150px] hidden sm:block"><flux:skeleton class="w-20 h-5"/></div>
                        <div class="flex-1 flex items-center gap-3">
                            <flux:skeleton class="w-8 h-8 rounded-md"/>
                            <flux:skeleton class="w-48 h-5"/>
                        </div>
                        <div class="w-[150px] hidden lg:block"><flux:skeleton class="w-24 h-5"/></div>
                        <div class="w-[100px] hidden sm:flex justify-center"><flux:skeleton class="w-16 h-6 rounded-md"/></div>
                        <div class="w-[100px] flex justify-center gap-2">
                            <flux:skeleton class="w-8 h-8 rounded-md"/>
                            <flux:skeleton class="w-8 h-8 rounded-md"/>
                        </div>
                    </div>
                    @endfor
                </flux:skeleton.group>
            </div>

            <!-- Table Body -->
            <div wire:loading.class="hidden" class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto w-full">
                @forelse($subjects as $item)
                <div wire:key="subject-{{ $item->id }}"
                     class="px-6 py-4 flex sm:justify-between sm:items-center hover:bg-gray-50/50 transition-colors gap-3">

                    <!-- Kode -->
                    <div class="text-gray-500 font-mono text-sm w-[150px] hidden sm:block">
                        {{ $item->subject_code }}
                    </div>

                    <!-- Nama -->
                    <div class="font-medium text-gray-800 text-sm flex-1 flex items-center gap-3">
                        <div class="h-8 w-8 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                        <div>
                            <span>{{ $item->subject_name }}</span>
                            <div class="sm:hidden text-xs font-mono text-gray-400 mt-0.5">{{ $item->subject_code }}</div>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div class="text-gray-600 text-sm w-[150px] hidden lg:block">
                        {{ $item->category }}
                    </div>

                    <!-- Status -->
                    <div class="w-[100px] hidden sm:flex justify-center shrink-0">
                        @if($item->is_active)
                            <span class="px-2 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="px-2 py-1 rounded-md text-xs font-bold bg-red-100 text-red-700">Nonaktif</span>
                        @endif
                    </div>

                    <!-- Aksi -->
                    <div class="w-[100px] flex items-center justify-center gap-2 shrink-0">
                        <button type="button" wire:click="openEditForm({{ $item->id }})"
                                class="p-1.5 rounded-md text-[#0F609B] hover:bg-blue-50 transition-colors"
                                title="Edit">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                            </svg>
                        </button>
                        <button type="button" wire:click="delete({{ $item->id }})"
                                wire:confirm="Hapus mata pelajaran '{{ $item->subject_name }}'?"
                                class="p-1.5 rounded-md text-red-500 hover:bg-red-50 transition-colors"
                                title="Hapus">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @empty
                <div class="p-8 flex flex-col items-center justify-center text-gray-500 h-64 text-sm">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                    Belum ada data pelajaran yang ditemukan.
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($subjects->hasPages())
            <div class="p-4 border-t border-gray-100 shrink-0 bg-white rounded-b-[20px]">
                {{ $subjects->links() }}
            </div>
            @endif

        </div>
    </div>

    <!-- ── MODAL TAMBAH / EDIT ──────────────────────────────────────────────── -->
    <div x-show="$wire.showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none"
         @keydown.escape.window="$wire.closeModal()">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="$wire.closeModal()"></div>

        <!-- Panel -->
        <div @click.stop
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-xl w-full max-w-md z-10">

            <!-- Header -->
            <div class="bg-[#0F609B] rounded-t-2xl px-6 py-4 flex items-center justify-between">
                <h3 class="text-white font-bold text-base">
                    {{ $editingId ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
                </h3>
                <button type="button" @click="$wire.closeModal()" class="text-white/70 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">

                <!-- Kode Mapel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kode Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="form_code"
                           type="text"
                           placeholder="Contoh: MTK-01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B] focus:border-transparent font-mono"
                           {{ $editingId ? 'readonly' : '' }}>
                    @error('form_code')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    @if($editingId)
                        <p class="text-xs text-gray-400 mt-1">Kode tidak dapat diubah setelah dibuat.</p>
                    @endif
                </div>

                <!-- Nama Pelajaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="form_name"
                           type="text"
                           placeholder="Contoh: Matematika"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B] focus:border-transparent">
                    @error('form_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="form_category"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B] focus:border-transparent bg-white">
                        <option value="Wajib">Wajib</option>
                        <option value="Muatan Lokal">Muatan Lokal</option>
                        <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                    </select>
                    @error('form_category')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Status Aktif</p>
                        <p class="text-xs text-gray-500">Pelajaran aktif tampil di jadwal dan input nilai</p>
                    </div>
                    <button type="button" wire:click="$toggle('form_active')" @click.prevent
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $form_active ? 'bg-[#0F609B]' : 'bg-gray-300' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform {{ $form_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>

            </div>

            <!-- Footer -->
            <div class="px-6 pb-6 flex justify-end gap-3">
                <button type="button" @click="$wire.closeModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="button" wire:click="save" wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm font-semibold text-white bg-[#0F609B] hover:bg-[#0a4f7f] rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2">
                    <span wire:loading wire:target="save">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Pelajaran' }}
                    </span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>

        </div>
    </div>

</div>

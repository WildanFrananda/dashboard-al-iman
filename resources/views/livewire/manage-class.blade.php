<div class="flex flex-col gap-6 h-full p-6">
    
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0 justify-between">
        
        <div class="text-white font-bold text-lg hidden md:block">
            Manage Kelas & Kurikulum
        </div>

        <!-- Search Input -->
        <div class="w-full md:w-[300px]">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari kelas..." class="w-full [&_input]:!rounded-full [&_input]:!bg-white [&_input]:!text-gray-800 [&_input::placeholder]:!text-gray-400 [&_svg]:!text-gray-400" />
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 w-full md:w-auto">
            <flux:button wire:click="openPromotion" variant="primary" icon="arrow-up-circle" class="flex-1 md:flex-none !bg-green-600 hover:!bg-green-700 !text-white !border-0">
                Kenaikan Kelas
            </flux:button>
            <flux:button wire:click="openForm" variant="primary" icon="plus" class="flex-1 md:flex-none !bg-[#F28B2B] hover:!bg-[#D97706] !text-white !border-0">
                Tambah Kelas
            </flux:button>
        </div>
    </div>

    <!-- MAIN CONTENT AREA (TABLE) -->
    <div class="relative bg-white rounded-[20px] shadow-sm flex-1 border border-gray-100 flex flex-col overflow-hidden min-h-[400px]">
        
        <!-- Table Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0 border-b border-gray-100">
            <div class="font-bold text-gray-800 text-sm w-[150px] hidden sm:block">Kode Kelas</div>
            <div class="font-bold text-gray-800 text-sm flex-1">Nama Kelas</div>
            <div class="font-bold text-gray-800 text-sm w-[200px] hidden md:block">Wali Kelas</div>
            <div class="font-bold text-gray-800 text-sm w-full sm:w-[100px] text-right sm:text-center mt-2 sm:mt-0">Aksi</div>
        </div>

        <!-- Table Body -->
        <div wire:loading.class="opacity-50" class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto w-full">
            @forelse($classes as $item)
            <div wire:key="class-{{ $item->id }}" class="px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center hover:bg-gray-50/50 transition-colors gap-3 sm:gap-0">
                
                <!-- Kode Kelas -->
                <div class="text-gray-500 font-mono text-sm w-full sm:w-[150px] hidden sm:block">
                    {{ $item->kode_kelas }}
                </div>

                <!-- Nama Kelas -->
                <div class="font-medium text-gray-800 text-sm flex-1 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-md bg-green-100 text-green-600 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $item->level ?? '?' }}
                    </div>
                    <span>{{ $item->nama_kelas }}</span>
                </div>

                <!-- Wali Kelas -->
                <div class="text-gray-500 text-sm w-[200px] hidden md:block">
                    {{ $item->waliKelas?->nama_lengkap ?? '-' }}
                </div>

                <!-- Actions -->
                <div class="w-full sm:w-[100px] flex items-center justify-end sm:justify-center gap-2">
                    <flux:button wire:click="edit({{ $item->id }})" variant="ghost" size="sm" icon="pencil-square" class="!text-[#0F609B] hover:!bg-blue-50" title="Edit" />
                    <flux:button wire:click="confirmDelete({{ $item->id }})" variant="ghost" size="sm" icon="trash" class="!text-red-500 hover:!bg-red-50" title="Delete" />
                </div>

            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-gray-500 h-64 text-sm">
                Belum ada data kelas yang ditemukan.
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($classes->hasPages())
        <div class="p-4 border-t border-gray-100 shrink-0 bg-white rounded-b-[20px]">
            {{ $classes->links() }}
        </div>
        @endif
    </div>

    <!-- CREATE/EDIT FORM MODAL -->
    <flux:modal wire:model="showForm" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</flux:heading>
                <flux:subheading>Isi detail informasi kelas di bawah ini.</flux:subheading>
            </div>

            <div class="grid gap-4">
                <flux:input wire:model="kode_kelas" label="Kode Kelas" placeholder="Contoh: SD-1A" />
                <flux:input wire:model="nama_kelas" label="Nama Kelas" placeholder="Contoh: Kelas 1A" />
                <flux:input wire:model="tahun_ajaran" label="Tahun Ajaran" placeholder="Contoh: 2024/2025" />
                
                <flux:select wire:model="wali_kelas_id" label="Wali Kelas" placeholder="Pilih Wali Kelas">
                    @foreach($gurus as $g)
                        <flux:select.option value="{{ $g->id }}">{{ $g->nama_lengkap }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="closeForm" variant="ghost">Batal</flux:button>
                <flux:button wire:click="save" variant="primary" class="!bg-[#0F609B]">{{ $editingId ? 'Simpan Perubahan' : 'Simpan Kelas' }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- PROMOTION MODAL (KENAIKAN KELAS) -->
    <flux:modal wire:model="showPromotionModal" class="md:w-[600px]">
        <div class="space-y-6">
            @if($promotionStep === 1)
                <div>
                    <flux:heading size="lg">Proses Kenaikan Kelas Otomatis</flux:heading>
                    <flux:subheading>Sistem akan menaikkan semua murid aktif ke tingkat berikutnya (Level + 1) untuk tahun ajaran baru.</flux:subheading>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 space-y-2">
                    <p class="text-sm text-blue-800 font-medium">Tahun Ajaran Baru yang akan diproses:</p>
                    <flux:input wire:model="newAcademicYear" placeholder="2025/2026" />
                    <p class="text-[10px] text-blue-600 mt-2">* Murid kelas 6 akan otomatis berstatus 'Lulus'.</p>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button wire:click="$set('showPromotionModal', false)" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="startPromotion" variant="primary" class="!bg-green-600">Lanjut ke Pilih Murid Tinggal Kelas</flux:button>
                </div>

            @elseif($promotionStep === 2)
                <div>
                    <flux:heading size="lg">Siapa yang Tinggal Kelas?</flux:heading>
                    <flux:subheading>Pilih murid yang TIDAK naik kelas. Murid yang tidak dipilih akan otomatis naik ke tingkat berikutnya.</flux:subheading>
                </div>

                <div class="max-h-[300px] overflow-y-auto border border-gray-100 rounded-lg divide-y divide-gray-100">
                    @foreach($activeStudents as $student)
                        <label class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" wire:model="selectedStayBackIds" value="{{ $student->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500 mr-3">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-800">{{ $student->nama_lengkap }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ $student->kelas()->wherePivot('tahun_ajaran', $this->tahun_ajaran)->first()?->nama_kelas ?? 'Tanpa Kelas' }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="flex items-center gap-2 text-red-600 text-xs italic">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span>Murid yang dicentang akan tetap di kelas lamanya di tahun ajaran {{ $newAcademicYear }}.</span>
                </div>

                <div class="flex gap-2">
                    <flux:button wire:click="$set('promotionStep', 1)" variant="ghost">Kembali</flux:button>
                    <flux:spacer />
                    <flux:button wire:click="processPromotion" variant="primary" class="!bg-[#0F609B]">Proses Kenaikan Kelas</flux:button>
                </div>
            @endif
        </div>
    </flux:modal>

    <!-- DELETE CONFIRMATION -->
    <flux:modal wire:model="confirmingDelete" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Kelas?</flux:heading>
                <flux:subheading>Tindakan ini tidak dapat dibatalkan.</flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost">Batal</flux:button>
                <flux:button wire:click="delete({{ $confirmingDelete }})" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>

</div>

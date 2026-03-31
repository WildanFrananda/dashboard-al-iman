<div class="flex flex-col gap-6 h-full p-6">
    
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0 justify-between">
        
        <div class="text-white font-bold text-lg hidden md:block">
            Manage Jadwal Mengajar
        </div>

        <!-- Search Input -->
        <div class="w-full md:w-[300px]">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari jadwal (Guru/Mapel/Kelas)..." class="w-full [&_input]:!rounded-full [&_input]:!bg-white [&_input]:!text-gray-800 [&_input::placeholder]:!text-gray-400 [&_svg]:!text-gray-400" />
        </div>

        <!-- Add Button -->
        <flux:button wire:click="openForm" variant="primary" icon="plus" class="w-full md:w-auto !bg-[#F28B2B] hover:!bg-[#D97706] !text-white !border-0">
            Tambah Jadwal
        </flux:button>
    </div>

    <!-- MAIN CONTENT AREA (TABLE) -->
    <div class="relative bg-white rounded-[20px] shadow-sm flex-1 border border-gray-100 flex flex-col overflow-hidden min-h-[400px]">
        
        <!-- Table Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0 border-b border-gray-100">
            <div class="font-bold text-gray-800 text-sm w-[120px]">Hari</div>
            <div class="font-bold text-gray-800 text-sm w-[120px]">Jam</div>
            <div class="font-bold text-gray-800 text-sm flex-1">Mata Pelajaran</div>
            <div class="font-bold text-gray-800 text-sm w-[200px]">Guru</div>
            <div class="font-bold text-gray-800 text-sm w-[120px]">Kelas</div>
            <div class="font-bold text-gray-800 text-sm w-[100px] text-center">Aksi</div>
        </div>

        <!-- Table Body -->
        <div wire:loading.class="opacity-50" class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto w-full">
            @forelse($schedules as $item)
            <div wire:key="schedule-{{ $item->id }}" class="px-6 py-4 flex justify-between items-center hover:bg-gray-50/50 transition-colors">
                
                <!-- Hari -->
                <div class="w-[120px]">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ 
                        $item->hari == 'Senin' ? 'bg-blue-100 text-blue-700' : (
                        $item->hari == 'Selasa' ? 'bg-green-100 text-green-700' : (
                        $item->hari == 'Rabu' ? 'bg-purple-100 text-purple-700' : (
                        $item->hari == 'Kamis' ? 'bg-orange-100 text-orange-700' : (
                        $item->hari == 'Jumat' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'))))
                    }}">
                        {{ $item->hari }}
                    </span>
                </div>

                <!-- Jam -->
                <div class="text-gray-600 text-sm w-[120px] font-mono">
                    {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                </div>

                <!-- Mata Pelajaran -->
                <div class="font-medium text-gray-800 text-sm flex-1">
                    {{ $item->subject?->subject_name }}
                </div>

                <!-- Guru -->
                <div class="text-gray-600 text-sm w-[200px]">
                    {{ $item->guru?->nama_lengkap }}
                </div>

                <!-- Kelas -->
                <div class="text-gray-600 text-sm w-[120px]">
                    <span class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $item->kelas?->nama_kelas }}</span>
                </div>

                <!-- Actions -->
                <div class="w-[100px] flex items-center justify-center gap-2">
                    <flux:button wire:click="edit({{ $item->id }})" variant="ghost" size="sm" icon="pencil-square" class="!text-[#0F609B] hover:!bg-blue-50" title="Edit" />
                    <flux:button wire:click="confirmDelete({{ $item->id }})" variant="ghost" size="sm" icon="trash" class="!text-red-500 hover:!bg-red-50" title="Delete" />
                </div>

            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-gray-500 h-64 text-sm">
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

    <!-- CREATE/EDIT FORM MODAL -->
    <flux:modal wire:model="showForm" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Jadwal' : 'Tambah Jadwal Baru' }}</flux:heading>
                <flux:subheading>Isi detail jadwal mengajar di bawah ini.</flux:subheading>
            </div>

            <div class="grid gap-4">
                <!-- Guru -->
                <flux:select wire:model="guru_id" label="Guru" placeholder="Pilih Guru">
                    @foreach($gurus as $g)
                        <flux:select.option value="{{ $g->id }}">{{ $g->nama_lengkap }} ({{ $g->nip }})</flux:select.option>
                    @endforeach
                </flux:select>

                <!-- Subject -->
                <flux:select wire:model="subject_id" label="Mata Pelajaran" placeholder="Pilih Mata Pelajaran">
                    @foreach($subjects as $s)
                        <flux:select.option value="{{ $s->id }}">{{ $s->subject_name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <!-- Kelas -->
                <flux:select wire:model="kelas_id" label="Kelas" placeholder="Pilih Kelas">
                    @foreach($classes as $c)
                        <flux:select.option value="{{ $c->id }}">{{ $c->nama_kelas }}</flux:select.option>
                    @endforeach
                </flux:select>

                <!-- Hari -->
                <flux:select wire:model="hari" label="Hari" placeholder="Pilih Hari">
                    @foreach($days as $day)
                        <flux:select.option value="{{ $day }}">{{ $day }}</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="jam_mulai" type="time" label="Jam Mulai" />
                    <flux:input wire:model="jam_selesai" type="time" label="Jam Selesai" />
                </div>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="closeForm" variant="ghost">Batal</flux:button>
                <flux:button wire:click="save" variant="primary" class="!bg-[#0F609B]">{{ $editingId ? 'Update Jadwal' : 'Simpan Jadwal' }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- DELETE CONFIRMATION -->
    <flux:modal wire:model="confirmingDelete" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Jadwal?</flux:heading>
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

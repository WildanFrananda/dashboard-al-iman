<div class="flex flex-col gap-6 h-full p-6">
    
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0 justify-between">
        
        <div class="text-white font-bold text-lg hidden md:block">
            Manage Murid
        </div>

        <!-- Search Input -->
        <div class="w-full md:w-[300px]">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari murid..." class="w-full [&_input]:!rounded-full [&_input]:!bg-white [&_input]:!text-gray-800 [&_input::placeholder]:!text-gray-400 [&_svg]:!text-gray-400" />
        </div>

        <!-- Year Selector -->
        <div class="w-full md:w-[150px]">
            <flux:select wire:model.live="selectedYear" class="[&_button]:!bg-white [&_button]:!rounded-full">
                @foreach($availableYears as $year)
                    <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <!-- Add Button -->
        <flux:button wire:click="openForm" variant="primary" icon="plus" class="w-full md:w-auto !bg-[#F28B2B] hover:!bg-[#D97706] !text-white !border-0">
            Daftarkan Murid Baru
        </flux:button>
    </div>

    <!-- STATUS FILTER TABS -->
    <div class="flex flex-wrap gap-3 shrink-0">
        <button wire:click="$set('selectedStatus', 'all')" 
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 
                {{ $selectedStatus === 'all' ? 'bg-[#0F609B] text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            Semua Murid
        </button>
        <button wire:click="$set('selectedStatus', 'aktif')" 
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 flex items-center gap-2
                {{ $selectedStatus === 'aktif' ? 'bg-green-600 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            Aktif
        </button>
        <button wire:click="$set('selectedStatus', 'lulus')" 
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 flex items-center gap-2
                {{ $selectedStatus === 'lulus' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            Lulus
        </button>
        <button wire:click="$set('selectedStatus', 'pindah')" 
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 flex items-center gap-2
                {{ $selectedStatus === 'pindah' ? 'bg-orange-600 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            Pindah/Keluar
        </button>
    </div>

    <!-- MAIN CONTENT AREA (TABLE) -->
    <div class="relative bg-white rounded-[20px] shadow-sm flex-1 border border-gray-100 flex flex-col overflow-hidden min-h-[400px]">
        
        <!-- Table Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0 border-b border-gray-100">
            <div class="font-bold text-gray-800 text-sm w-[120px]">NIS</div>
            <div class="font-bold text-gray-800 text-sm flex-1">Nama Murid</div>
            <div class="font-bold text-gray-800 text-sm w-[120px]">Kelas</div>
            <div class="font-bold text-gray-800 text-sm w-[120px]">Status</div>
            <div class="font-bold text-gray-800 text-sm w-[100px] text-center">Aksi</div>
        </div>

        <!-- Table Body -->
        <div wire:loading.class="opacity-50" class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto w-full">
            @forelse($students as $item)
            <div wire:key="student-{{ $item->id }}" class="px-6 py-4 flex justify-between items-center hover:bg-gray-50/50 transition-colors">
                
                <!-- NIS -->
                <div class="text-gray-500 font-mono text-sm w-[120px]">
                    {{ $item->nis }}
                </div>

                <!-- Nama Murid -->
                <div class="font-medium text-gray-800 text-sm flex-1 flex flex-col">
                    <span>{{ $item->nama_lengkap }}</span>
                    <span class="text-xs text-gray-400">{{ $item->user->email }}</span>
                </div>

                <!-- Kelas -->
                <div class="text-gray-600 text-sm w-[120px]">
                    @php $k = $item->kelas->first(); @endphp
                    @if($k)
                        <span class="px-2 py-1 rounded bg-gray-100 text-xs">
                            {{ $k->nama_kelas }}
                        </span>
                    @elseif($item->status === 'lulus')
                        <span class="text-gray-400 text-xs italic">Alumni</span>
                    @else
                        <span class="text-gray-400 text-xs">Belum Assign</span>
                    @endif
                </div>

                <!-- Status -->
                <div class="w-[120px]">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ 
                        $item->status == 'aktif' ? 'bg-green-100 text-green-700' : (
                        $item->status == 'lulus' ? 'bg-blue-100 text-blue-700' : (
                        $item->status == 'pindah' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700'))
                    }}">
                        {{ $item->status }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="w-[100px] flex items-center justify-center gap-2">
                    <flux:button wire:click="edit({{ $item->id }})" variant="ghost" size="sm" icon="pencil-square" class="!text-[#0F609B] hover:!bg-blue-50" title="Edit" />
                    <flux:button variant="ghost" size="sm" icon="trash" class="!text-red-500 hover:!bg-red-50" title="Delete" />
                </div>

            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-gray-500 h-64 text-sm">
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

    <!-- REGISTER/EDIT FORM MODAL -->
    <flux:modal wire:model="showForm" class="md:w-[600px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Data Murid' : 'Daftarkan Murid Baru' }}</flux:heading>
                <flux:subheading>Input informasi murid dan kirim untuk memproses.</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 md:col-span-1">
                    <flux:input wire:model="name" label="Nama Panggilan User" placeholder="Contoh: Budi" />
                </div>
                <div class="col-span-2 md:col-span-1">
                    <flux:input wire:model="email" type="email" label="Email Login" placeholder="budi@email.com" />
                </div>
                <div class="col-span-2 md:col-span-1">
                    <flux:input wire:model="password" type="password" label="Password Login" placeholder="{{ $editingId ? 'Kosongkan jika tidak ganti' : 'Minimal 6 karakter' }}" />
                </div>
                <div class="col-span-2 md:col-span-1">
                    <flux:input wire:model="nis" label="NIS (Nomor Induk Siswa)" placeholder="Contoh: 20230001" />
                </div>
                <div class="col-span-2">
                    <flux:input wire:model="nama_lengkap" label="Nama Lengkap Sesuai Akte" placeholder="Nama lengkap murid" />
                </div>

                <div class="col-span-2 md:col-span-1">
                    <flux:select wire:model="kelas_id" label="Pilih Kelas" placeholder="Pilih Kelas">
                        @foreach($classes as $c)
                            <flux:select.option value="{{ $c->id }}">{{ $c->nama_kelas }} ({{ $c->tahun_ajaran }})</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <flux:select wire:model="status" label="Status Murid">
                        <flux:select.option value="aktif">Aktif</flux:select.option>
                        <flux:select.option value="lulus">Lulus</flux:select.option>
                        <flux:select.option value="pindah">Pindah</flux:select.option>
                        <flux:select.option value="keluar">Keluar (Dikeluarkan)</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="closeForm" variant="ghost">Batal</flux:button>
                <flux:button wire:click="save" variant="primary" class="!bg-[#0F609B]">{{ $editingId ? 'Simpan Perubahan' : 'Daftarkan Murid' }}</flux:button>
            </div>
        </div>
    </flux:modal>

</div>

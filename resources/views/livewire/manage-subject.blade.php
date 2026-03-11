<div class="flex flex-col gap-6 h-full">
    
    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0 justify-between">
        
        <div class="text-white font-bold text-lg hidden md:block">
            Manage Pelajaran
        </div>

        <!-- Search Input -->
        <div class="w-full md:w-[300px]">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari pelajaran (kode/nama)..." class="w-full [&_input]:!rounded-full [&_input]:!bg-white [&_input]:!text-gray-800 [&_input::placeholder]:!text-gray-400 [&_svg]:!text-gray-400" />
        </div>

        <!-- Add Button -->
        <flux:button variant="primary" icon="plus" class="w-full md:w-auto !bg-[#F28B2B] hover:!bg-[#D97706] !text-white !border-0">
            Tambah Pelajaran
        </flux:button>
    </div>

    <!-- MAIN CONTENT AREA (TABLE) -->
    <div class="relative bg-white rounded-[20px] shadow-sm flex-1 border border-gray-100 flex flex-col overflow-hidden min-h-[400px]">
        
        <!-- Table Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0 border-b border-gray-100">
            <div class="font-bold text-gray-800 text-sm w-[150px] hidden sm:block">Kode Mapel</div>
            <div class="font-bold text-gray-800 text-sm flex-1">Nama Pelajaran</div>
            <div class="font-bold text-gray-800 text-sm w-full sm:w-[100px] text-right sm:text-center mt-2 sm:mt-0">Aksi</div>
        </div>

        <!-- Skeleton Loading State -->
        <div wire:loading class="w-full divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto">
            <flux:skeleton.group animate="shimmer">
                @for ($i = 0; $i < 5; $i++)
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0">
                    <!-- Kode Mapel Skeleton -->
                    <div class="w-full sm:w-[150px] hidden sm:block">
                        <flux:skeleton class="w-20 h-5" />
                    </div>
                    <!-- Nama Pelajaran Skeleton -->
                    <div class="flex-1 flex items-center gap-3">
                        <flux:skeleton class="w-8 h-8 rounded-md" />
                        <flux:skeleton class="w-1/2 sm:w-[250px]" />
                    </div>
                    <!-- Actions Skeleton -->
                    <div class="w-full sm:w-[100px] flex items-center justify-end sm:justify-center gap-2">
                        <flux:skeleton class="w-8 h-8 rounded-md" />
                        <flux:skeleton class="w-8 h-8 rounded-md" />
                    </div>
                </div>
                @endfor
            </flux:skeleton.group>
        </div>

        <!-- Table Body -->
        <div wire:loading.class="hidden" class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto w-full">
            @forelse($pelajarans as $pelajaran)
            <div wire:key="pelajaran-{{ $pelajaran->id }}" class="px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center hover:bg-gray-50/50 transition-colors gap-3 sm:gap-0">
                
                <!-- Kode Mapel Desktop -->
                <div class="text-gray-500 font-mono text-sm w-full sm:w-[150px] hidden sm:block">
                    {{ $pelajaran->kode_mapel }}
                </div>

                <!-- Nama Pelajaran -->
                <div class="font-medium text-gray-800 text-sm flex-1 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <span>{{ $pelajaran->nama_mapel }}</span>
                    <!-- Mobile Kode Mapel -->
                    <div class="sm:hidden ml-auto">
                        <span class="text-xs px-2.5 py-1 rounded-sm font-mono bg-gray-100 text-gray-700">
                            {{ $pelajaran->kode_mapel }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="w-full sm:w-[100px] flex items-center justify-end sm:justify-center gap-2">
                    <flux:button variant="ghost" size="sm" icon="pencil-square" class="!text-[#0F609B] hover:!bg-blue-50" title="Edit" />
                    <flux:button variant="ghost" size="sm" icon="trash" class="!text-red-500 hover:!bg-red-50" title="Delete" />
                </div>

            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-gray-500 h-64 text-sm">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                Belum ada data pelajaran yang ditemukan.
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($pelajarans->hasPages())
        <div class="p-4 border-t border-gray-100 shrink-0 bg-white rounded-b-[20px]">
            {{ $pelajarans->links() }}
        </div>
        @endif

    </div>

</div>

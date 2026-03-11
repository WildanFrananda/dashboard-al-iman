<div class="flex flex-col gap-6 h-full">
    
    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0 justify-between">
        
        <div class="text-white font-bold text-lg hidden md:block">
            Manage Users
        </div>

        <!-- Search Input -->
        <div class="w-full md:w-[300px] flex items-center gap-3">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari user (nama/email)..." 
                       class="w-full bg-white border-0 rounded-md pl-10 pr-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-white">
            </div>
        </div>

        <!-- Add Button -->
        <button class="w-full md:w-auto bg-[#F28B2B] hover:bg-[#D97706] text-white font-medium py-2.5 px-6 rounded-md transition-colors shrink-0 flex items-center gap-2 justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah User
        </button>
    </div>

    <!-- ROLE FILTER TABS -->
    <div class="flex flex-wrap gap-3 shrink-0">
        <button wire:click="$set('selectedRole', 'all')" 
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 
                {{ $selectedRole === 'all' ? 'bg-[#0F609B] text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            Semua
        </button>
        <button wire:click="$set('selectedRole', 'admin')" 
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 flex items-center gap-2
                {{ $selectedRole === 'admin' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            <span class="w-2 h-2 rounded-full {{ $selectedRole === 'admin' ? 'bg-white' : 'bg-purple-500' }}"></span>
            Admin
        </button>
        <button wire:click="$set('selectedRole', 'guru')" 
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 flex items-center gap-2
                {{ $selectedRole === 'guru' ? 'bg-green-600 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            <span class="w-2 h-2 rounded-full {{ $selectedRole === 'guru' ? 'bg-white' : 'bg-green-500' }}"></span>
            Guru
        </button>
        <button wire:click="$set('selectedRole', 'murid')" 
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 flex items-center gap-2
                {{ $selectedRole === 'murid' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            <span class="w-2 h-2 rounded-full {{ $selectedRole === 'murid' ? 'bg-white' : 'bg-blue-500' }}"></span>
            Murid
        </button>
    </div>

    <!-- MAIN CONTENT AREA (TABLE) -->
    <div class="relative bg-white rounded-[20px] shadow-sm flex-1 border border-gray-100 flex flex-col overflow-hidden min-h-[400px]">
        
        <!-- Loading Overlay -->
        <div wire:loading.flex class="absolute inset-0 z-10 bg-white/50 backdrop-blur-sm flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <svg class="animate-spin h-8 w-8 text-[#0F609B]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-500">Memuat data...</span>
            </div>
        </div>

        <!-- Table Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0 border-b border-gray-100">
            <div class="font-bold text-gray-800 text-sm flex-1">Nama</div>
            <div class="font-bold text-gray-800 text-sm w-[250px] hidden md:block">Email</div>
            <div class="font-bold text-gray-800 text-sm w-[150px] hidden sm:block">Role</div>
            <div class="font-bold text-gray-800 text-sm w-full sm:w-[100px] text-right sm:text-center mt-2 sm:mt-0">Aksi</div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto relative">
            @forelse($users as $user)
            <div wire:key="user-{{ $user->id }}" class="px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center hover:bg-gray-50/50 transition-colors gap-3 sm:gap-0">
                
                <!-- User Name -->
                <div class="font-medium text-gray-800 text-sm flex-1 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $user->initials() }}
                    </div>
                    <span>{{ $user->name }}</span>
                    <!-- Mobile Role Badge -->
                    <span class="sm:hidden ml-auto text-xs px-2 py-0.5 rounded-full capitalize 
                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'guru' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ $user->role }}
                    </span>
                </div>

                <!-- Email -->
                <div class="text-gray-500 text-sm w-full sm:w-[250px] hidden md:block">
                    {{ $user->email }}
                </div>

                <!-- Role Badge Desktop -->
                <div class="w-full sm:w-[150px] hidden sm:block">
                    @php
                        $roleColors = [
                            'admin' => 'bg-purple-100 text-purple-700',
                            'guru' => 'bg-green-100 text-green-700',
                            'murid' => 'bg-blue-100 text-blue-700',
                        ];
                        $colorClass = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <span class="{{ $colorClass }} px-3 py-1 rounded-full text-xs font-bold capitalize">
                        {{ $user->role }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="w-full sm:w-[100px] flex items-center justify-end sm:justify-center gap-4">
                    <button class="text-[#0F609B] hover:text-blue-800 transition-colors tooltip" title="Edit">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                    <button class="text-red-500 hover:text-red-700 transition-colors tooltip" title="Delete">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>

            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-gray-500 h-64 text-sm">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Belum ada data user yang ditemukan.
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="p-4 border-t border-gray-100 shrink-0 bg-white rounded-b-[20px]">
            {{ $users->links() }}
        </div>
        @endif

    </div>

</div>

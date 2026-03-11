<div class="flex flex-col gap-6 h-full">
    
    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0 justify-between">
        
        <div class="text-white font-bold text-lg hidden md:block">
            Manage Users
        </div>

        <!-- Search Input -->
        <div class="w-full md:w-[300px]">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari user (nama/email)..." class="w-full [&_input]:!rounded-full [&_input]:!bg-white [&_input]:!text-gray-800 [&_input::placeholder]:!text-gray-400 [&_svg]:!text-gray-400" />
        </div>

        <!-- Add Button -->
        <flux:button variant="primary" icon="plus" class="w-full md:w-auto !bg-[#F28B2B] hover:!bg-[#D97706] !text-white !border-0">
            Tambah User
        </flux:button>
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
        
        <!-- Table Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0 border-b border-gray-100">
            <div class="font-bold text-gray-800 text-sm flex-1">Nama</div>
            <div class="font-bold text-gray-800 text-sm w-[250px] hidden md:block">Email</div>
            <div class="font-bold text-gray-800 text-sm w-[150px] hidden sm:block">Role</div>
            <div class="font-bold text-gray-800 text-sm w-full sm:w-[100px] text-right sm:text-center mt-2 sm:mt-0">Aksi</div>
        </div>

        <!-- Skeleton Loading State -->
        <div wire:loading class="w-full divide-y divide-dashed divide-gray-200 flex-1 overflow-y-auto">
            <flux:skeleton.group animate="shimmer">
                @for ($i = 0; $i < 5; $i++)
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0">
                    <!-- User Name Skeleton -->
                    <div class="flex-1 flex items-center gap-3">
                        <flux:skeleton class="w-8 h-8 rounded-full" />
                        <flux:skeleton class="w-1/2 sm:w-[150px]" />
                    </div>
                    <!-- Email Skeleton -->
                    <div class="w-full sm:w-[250px] hidden md:block">
                        <flux:skeleton class="w-3/4" />
                    </div>
                    <!-- Role Badge Skeleton -->
                    <div class="w-full sm:w-[150px] hidden sm:block">
                        <flux:skeleton class="w-16 h-6 rounded-full" />
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
            @forelse($users as $user)
            <div wire:key="user-{{ $user->id }}" class="px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center hover:bg-gray-50/50 transition-colors gap-3 sm:gap-0">
                
                <!-- User Name -->
                <div class="font-medium text-gray-800 text-sm flex-1 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $user->initials() }}
                    </div>
                    <span>{{ $user->name }}</span>
                    <!-- Mobile Role Badge -->
                    <div class="sm:hidden ml-auto">
                        <span class="text-xs px-2.5 py-1 rounded-full font-bold capitalize {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'guru' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $user->role }}
                        </span>
                    </div>
                </div>

                <!-- Email -->
                <div class="text-gray-500 text-sm w-full sm:w-[250px] hidden md:block">
                    {{ $user->email }}
                </div>

                <!-- Role Badge Desktop -->
                <div class="w-full sm:w-[150px] hidden sm:block">
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold capitalize {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'guru' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ $user->role }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="w-full sm:w-[100px] flex items-center justify-end sm:justify-center gap-2">
                    <flux:button variant="ghost" size="sm" icon="pencil-square" class="!text-[#0F609B] hover:!bg-blue-50" title="Edit" />
                    <flux:button variant="ghost" size="sm" icon="trash" class="!text-red-500 hover:!bg-red-50" title="Delete" />
                </div>

            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-gray-500 h-64 text-sm">
                <flux:icon.users class="w-12 h-12 text-gray-300 mb-3" />
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

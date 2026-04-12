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
        <button wire:click="openAddForm"
            class="inline-flex items-center gap-2 rounded-lg bg-[#F28B2B] hover:bg-[#D97706] px-4 py-2.5 text-sm font-semibold text-white transition-colors w-full md:w-auto justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah User
        </button>
    </div>

    <!-- Flash Message -->
    @if(session('user_message'))
        <div class="flex items-center gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('user_message') }}
        </div>
    @endif
    @if(session('user_error'))
        <div class="flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            {{ session('user_error') }}
        </div>
    @endif

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
                    <div class="flex-1 flex items-center gap-3">
                        <flux:skeleton class="w-8 h-8 rounded-full" />
                        <flux:skeleton class="w-1/2 sm:w-[150px]" />
                    </div>
                    <div class="w-full sm:w-[250px] hidden md:block">
                        <flux:skeleton class="w-3/4" />
                    </div>
                    <div class="w-full sm:w-[150px] hidden sm:block">
                        <flux:skeleton class="w-16 h-6 rounded-full" />
                    </div>
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
                    <div class="sm:hidden ml-auto">
                        <span class="text-xs px-2.5 py-1 rounded-full font-bold capitalize
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'guru' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
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
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold capitalize
                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'guru' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ $user->role }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="w-full sm:w-[100px] flex items-center justify-end sm:justify-center gap-2">
                    <button wire:click="openEditForm({{ $user->id }})"
                        class="p-1.5 rounded-md text-[#0F609B] hover:bg-blue-50 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button wire:click="deleteUser({{ $user->id }})"
                        wire:confirm="Yakin ingin menghapus user '{{ $user->name }}'?"
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
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

    <!-- ── MODAL FORM ──────────────────────────────────────────────────────── -->
    <div
        x-data
        x-show="$wire.showModal"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        x-cloak
        @keydown.escape.window="$wire.closeModal()"
    >
        <div
            x-show="$wire.showModal"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl w-full max-w-md"
            @click.stop
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900 text-base">
                    {{ $editingId ? 'Edit User' : 'Tambah User Baru' }}
                </h2>
                <button wire:click="closeModal" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form wire:submit="saveUser" class="p-6 space-y-4">

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text"
                        wire:model="form_name"
                        placeholder="Masukkan nama lengkap"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                               {{ $errors->has('form_name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error('form_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email"
                        wire:model="form_email"
                        placeholder="contoh@email.com"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                               {{ $errors->has('form_email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error('form_email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                    <select wire:model="form_role"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                               {{ $errors->has('form_role') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        <option value="murid">Murid</option>
                        <option value="guru">Guru</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('form_role')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Password
                        @if($editingId)
                            <span class="text-xs font-normal text-gray-400 ml-1">(kosongkan jika tidak ingin mengubah)</span>
                        @endif
                    </label>
                    <input type="password"
                        wire:model="form_password"
                        placeholder="{{ $editingId ? 'Password baru (opsional)' : 'Minimal 8 karakter' }}"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B]
                               {{ $errors->has('form_password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error('form_password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 mt-2">
                    <button type="button" wire:click="closeModal"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-[#0F609B] hover:bg-[#0d5289] transition-colors">
                        <svg wire:loading.remove class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                        </svg>
                        <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <span wire:loading.remove>{{ $editingId ? 'Simpan Perubahan' : 'Tambah User' }}</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

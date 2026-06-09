<div x-data="{ showPassword: false }">
    
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="font-heading text-3xl font-bold text-gray-900 mb-2">
            Log in to your account
        </h1>
        <p class="text-gray-500 text-sm font-sans">
            Welcome back! Please enter your details.
        </p>
    </div>

    <!-- Login Form -->
    <form wire:submit.prevent="login" class="space-y-5">
        
        <!-- Email Input -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email
            </label>
            <input 
                wire:model="email"
                type="email" 
                id="email" 
                placeholder="Enter your email"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-brand-600 focus:ring focus:ring-brand-600/20 transition-all outline-none placeholder:text-gray-400 font-sans text-sm"
            >
            @error('email') 
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Password Input -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Password
            </label>
            <div class="relative">
                <input
                    wire:model="password"
                    type="password"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    id="password"
                    placeholder="••••••••"
                    class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:border-brand-600 focus:ring focus:ring-brand-600/20 transition-all outline-none placeholder:text-gray-400 font-sans text-sm"
                >
                <!-- Toggle Password Visibility (Alpine) -->
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    x-bind:aria-label="showPassword ? 'Hide password' : 'Show password'"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600"
                >
                    <!-- Eye (password hidden) -->
                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <!-- Eye-slash (password shown) -->
                    <svg x-show="showPassword" style="display:none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.243 4.243L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password') 
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Options: Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input 
                    wire:model="remember"
                    id="remember-me" 
                    type="checkbox" 
                    class="h-4 w-4 text-brand-600 focus:ring-brand-600 border-gray-300 rounded cursor-pointer"
                >
                <label for="remember-me" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">
                    Remember me
                </label>
            </div>

            <!-- Ganti href dengan route livewire nantinya -->
            <a href="#" class="text-sm font-semibold text-gray-900 hover:text-brand-600 hover:underline">
                Forgot password
            </a>
        </div>

        <!-- Main Button -->
        <flux:button 
            type="submit" 
            variant="primary" 
            class="w-full !bg-brand-600 hover:!bg-brand-700 !border-0 !text-white"
        >
            Sign in
        </flux:button>

    </form>

    <!-- Footer -->
    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
            Don't have an account? 
            <a href="#" class="font-semibold text-gray-900 hover:text-brand-600 hover:underline">
                Contact school administration
            </a>
        </p>
    </div>
</div>
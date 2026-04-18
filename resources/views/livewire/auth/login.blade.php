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
                    :type="showPassword ? 'text' : 'password'" 
                    id="password" 
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-brand-600 focus:ring focus:ring-brand-600/20 transition-all outline-none placeholder:text-gray-400 font-sans text-sm"
                >
                <!-- Toggle Password Visibility Icon (Alpine Logic) -->
                <button 
                    type="button" 
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600"
                >
                    <!-- Eye Icon Placeholder -->
                    <template x-if="!showPassword">
                         <img src="https://placehold.co/20x20/transparent/9CA3AF?text=Eye" alt="Show" class="w-5 h-5 opacity-50">
                    </template>
                    <template x-if="showPassword">
                         <img src="https://placehold.co/20x20/transparent/9CA3AF?text=Hide" alt="Hide" class="w-5 h-5 opacity-50">
                    </template>
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
            class="w-full !bg-brand-600 hover:!bg-brand-700 !border-0"
        >
            Sign in
        </flux:button>

        <!-- Google Button -->
        <flux:button 
            type="button" 
            variant="outline" 
            class="w-full text-gray-700 flex items-center justify-center"
        >
            <img src="{{ asset('img/google.png') }}" alt="Google" class="w-5 h-5 mr-2">
            Sign in with Google
        </flux:button>

    </form>

    <!-- Footer -->
    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
            Don't have an account? 
            <a href="#" class="font-semibold text-gray-900 hover:text-brand-600 hover:underline">
                Sign up
            </a>
        </p>
    </div>
</div>
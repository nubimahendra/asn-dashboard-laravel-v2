@if (session('success') || session('error'))
<div x-data="{ show: false }"
     x-init="setTimeout(() => show = true, 50); setTimeout(() => show = false, 3000)"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-x-full"
     x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-0 translate-x-full"
     class="fixed top-5 right-5 z-[9999] max-w-sm w-full bg-white dark:bg-gray-800 shadow-xl rounded-lg border-l-4 {{ session('success') ? 'border-green-500' : 'border-red-500' }} flex items-start p-4"
     style="display: none;">
    
    <!-- Icon -->
    <div class="flex-shrink-0">
        @if (session('success'))
            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @else
            <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @endif
    </div>

    <!-- Content -->
    <div class="ml-3 w-0 flex-1 pt-0.5">
        <p class="text-sm font-medium {{ session('success') ? 'text-green-800 dark:text-green-400' : 'text-red-800 dark:text-red-400' }}">
            {{ session('success') ? 'Berhasil!' : 'Error!' }}
        </p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            {{ session('success') ?? session('error') }}
        </p>
    </div>

    <!-- Close Button -->
    <div class="ml-4 flex-shrink-0 flex">
        <button @click="show = false" type="button" class="bg-transparent rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
            <span class="sr-only">Close</span>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
@endif

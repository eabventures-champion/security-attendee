<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-gray-800 p-10 rounded-2xl shadow-xl border border-gray-700 text-center">
            
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-500/20 mb-6">
                <svg class="h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            
            <h2 class="mt-6 text-2xl font-extrabold text-white tracking-tight">
                Invalid or Expired Link
            </h2>
            
            <p class="mt-4 text-gray-400 text-base">
                We couldn't verify your email address. The verification link you clicked may be invalid, or it might have already expired or been used.
            </p>
            
            <div class="mt-8 pt-6 border-t border-gray-700">
                <p class="text-sm text-gray-500">Need help?</p>
                <a href="#" class="mt-2 inline-block font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                    Contact the event organizer
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>

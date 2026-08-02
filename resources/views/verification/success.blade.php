<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-900 bg-gradient-to-br from-gray-900 via-gray-800 to-indigo-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-gray-800/80 backdrop-blur-sm p-10 rounded-2xl shadow-2xl border border-gray-700 relative overflow-hidden">
            <!-- Confetti effect base -->
            <div class="absolute inset-0 z-0 opacity-20 pointer-events-none" 
                 style="background-image: radial-gradient(circle at center, #818cf8 2px, transparent 2px), radial-gradient(circle at center, #34d399 2px, transparent 2px); background-size: 30px 30px, 40px 40px; background-position: 0 0, 15px 15px;">
            </div>
            
            <div class="relative z-10 text-center">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-500/20 mb-6 animate-pulse">
                    <svg class="h-16 w-16 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <h2 class="mt-6 text-3xl font-extrabold text-white tracking-tight">
                    Email Verified Successfully!
                </h2>
                
                <p class="mt-4 text-gray-300 text-lg">
                    Hi <span class="font-semibold text-white">{{ $attendee->full_name }}</span>, you're all set for 
                    <span class="font-semibold text-indigo-400">{{ $event->name }}</span>.
                </p>
                
                <div class="mt-6 bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                    <p class="text-sm text-gray-400 mb-2">Your QR code has been generated and sent to your email.</p>
                    <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download QR Code
                    </a>
                </div>
                
                <div class="mt-8 bg-gray-800 rounded-xl p-5 border border-gray-600 text-left">
                    <h3 class="text-lg font-medium text-white mb-3 border-b border-gray-700 pb-2">Event Details</h3>
                    <div class="space-y-3">
                        <div class="flex items-start text-sm text-gray-300">
                            <svg class="mr-3 h-5 w-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $event->starts_at?->format('F j, Y g:i A') ?? 'TBA' }}</span>
                        </div>
                        <div class="flex items-start text-sm text-gray-300">
                            <svg class="mr-3 h-5 w-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $event->venue ?? 'Online' }}</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-700 text-center">
                        <a href="#" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                            + Add to Calendar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

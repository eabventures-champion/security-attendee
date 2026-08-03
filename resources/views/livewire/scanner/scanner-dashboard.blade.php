<div x-data="{ showMobileSearch: false }" class="min-h-[calc(100vh-5rem)] rounded-2xl border border-white/10 flex flex-col md:flex-row bg-slate-950 text-slate-100 font-inter overflow-hidden shadow-2xl relative">
    <!-- Load QR Scanning Libraries for Live Scanner & Local QR File Uploads -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <div id="qr-reader-file-temp" style="display:none;"></div>
@if(!$event || !$gate)
    <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
        </div>
        <h2 class="text-2xl font-extrabold text-white">Scanner Not Configured</h2>
        <p class="text-slate-400 text-sm max-w-md">No active event or gate was found. Please create an event and assign a scanner gate first.</p>
        <a href="{{ route('events.index') }}" class="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition-all">
            Go to Events
        </a>
    </div>
@else
    <!-- Left Panel: Scanner & Results -->
    <div class="flex-1 flex flex-col relative">
        <!-- Top Bar -->
        <div class="h-16 border-b border-white/10 bg-slate-900/50 backdrop-blur-md flex items-center justify-between px-4 sm:px-6 z-20">
            <div class="flex items-center space-x-2.5 min-w-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.8)] shrink-0"></span>
                <h1 class="text-sm sm:text-lg font-bold text-white truncate">{{ $gate->name }}</h1>
                <span class="text-slate-500 text-xs sm:text-sm truncate hidden sm:inline">| {{ $event->name }}</span>
            </div>
            <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                <button type="button" onclick="initLiveCameraScanner()" class="px-3 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-600 text-emerald-400 hover:text-white border border-emerald-500/30 font-bold text-xs cursor-pointer flex items-center gap-1.5 transition-all shadow-sm" title="Request camera permission & start live phone/webcam stream">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span class="hidden sm:inline">Live Camera</span>
                </button>
                <input type="file" id="qr-file-input" accept="image/*" class="hidden" onchange="handleQrFileUpload(event)">
                <label for="qr-file-input" class="px-3 py-1.5 rounded-xl bg-blue-500/10 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-500/30 font-bold text-xs cursor-pointer flex items-center gap-1.5 transition-all shadow-sm" title="Upload a QR code image file to test locally">
                    <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="hidden sm:inline">Upload QR Image</span>
                </label>
                @if($event->capacity)
                    <div class="text-[11px] sm:text-xs text-slate-400 text-right px-2.5 py-1 rounded-xl bg-slate-800/80 border border-slate-700/80">
                        <div class="font-bold {{ $stats['granted'] >= $event->capacity ? 'text-rose-400 font-extrabold' : 'text-emerald-400' }}">
                            {{ $stats['granted'] }} / {{ $event->capacity }}
                        </div>
                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-medium">Capacity</span>
                    </div>
                @endif
                <div class="text-[11px] sm:text-xs text-slate-400 text-right">
                    <div class="font-semibold text-white">{{ $stats['granted'] }} / {{ $stats['total'] }}</div>
                    <span class="text-[9px] sm:text-[11px]">Scans Today</span>
                </div>
                <a href="{{ route('dashboard') }}" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 transition-colors text-slate-300" title="Return to Dashboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            </div>
        </div>

        <!-- Main Scanner Area -->
        <div class="flex-1 relative flex items-center justify-center bg-black overflow-hidden">
            
            <!-- Live Camera Video Container -->
            <div class="w-full h-full absolute inset-0 overflow-hidden bg-slate-950 flex items-center justify-center">
                <div id="qr-reader-video" class="w-full h-full object-cover"></div>
            </div>

            <!-- Viewfinder Overlay -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                <div class="w-64 h-64 border-2 border-white/30 rounded-3xl relative shadow-[0_0_0_9999px_rgba(0,0,0,0.5)]">
                    <!-- Corner markers -->
                    <div class="absolute -top-1 -left-1 w-8 h-8 border-t-4 border-l-4 border-blue-500 rounded-tl-3xl"></div>
                    <div class="absolute -top-1 -right-1 w-8 h-8 border-t-4 border-r-4 border-blue-500 rounded-tr-3xl"></div>
                    <div class="absolute -bottom-1 -left-1 w-8 h-8 border-b-4 border-l-4 border-blue-500 rounded-bl-3xl"></div>
                    <div class="absolute -bottom-1 -right-1 w-8 h-8 border-b-4 border-r-4 border-blue-500 rounded-br-3xl"></div>
                    
                    <!-- Scanning line animation -->
                    <div class="absolute top-0 left-0 w-full h-1 bg-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.8)] animate-scan"></div>
                </div>
            </div>

            <!-- Camera Status Banner (Permission prompt / Retry button) -->
            <div id="camera-status-overlay" class="absolute top-4 inset-x-4 z-20 hidden">
                <div class="p-3 rounded-2xl bg-slate-900/90 border border-amber-500/40 backdrop-blur-md text-amber-200 text-xs font-semibold flex items-center justify-between shadow-2xl">
                    <div class="flex items-center gap-2">
                        <span class="animate-pulse">📷</span>
                        <span id="camera-status-text">Requesting camera permission...</span>
                    </div>
                    <button type="button" onclick="initLiveCameraScanner()" class="px-3 py-1 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-[11px] uppercase tracking-wider cursor-pointer shadow-md">
                        Allow / Start Camera
                    </button>
                </div>
            </div>

            <!-- Overlays for Results -->
            @if($scanResult)
                @php
                    $resultStr = is_object($scanResult) ? $scanResult->value : (string)$scanResult;
                @endphp
                <div class="absolute inset-0 z-30 flex flex-col items-center justify-center backdrop-blur-xl transition-all duration-300
                    {{ $resultStr === 'granted' ? 'bg-emerald-900/90' : ($resultStr === 'denied' ? 'bg-rose-900/90' : 'bg-amber-900/90') }}"
                    wire:click="clearResult">
                    
                    <div class="text-center transform transition-transform animate-bounce-in">
                        @if($resultStr === 'granted')
                            <div class="w-32 h-32 rounded-full bg-emerald-500 flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(16,185,129,0.5)]">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        @elseif($resultStr === 'denied')
                            <div class="w-32 h-32 rounded-full bg-rose-500 flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(244,63,94,0.5)]">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                        @else
                            <div class="w-32 h-32 rounded-full bg-amber-500 flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(245,158,11,0.5)]">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        @endif

                        <h2 class="text-4xl font-bold text-white mb-2">{{ strtoupper($resultStr) }}</h2>
                        <p class="text-xl text-white/80 mb-8">{{ $scanMessage }}</p>

                        @if($scannedAttendee)
                            <div class="bg-black/20 p-6 rounded-2xl backdrop-blur-md border border-white/10 w-full max-w-sm mx-auto text-left">
                                <p class="text-sm text-white/60 mb-1">Attendee Info</p>
                                <p class="text-2xl font-bold text-white mb-1">{{ $scannedAttendee->full_name }}</p>
                                <div class="flex items-center space-x-2 mt-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium border border-white/20 bg-white/10 text-white uppercase tracking-wider">
                                        {{ is_object($scannedAttendee->access_role) ? $scannedAttendee->access_role->label() : ucfirst($scannedAttendee->access_role) }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        
                        <p class="text-white/50 text-sm mt-8 animate-pulse">Tap anywhere to continue</p>
                    </div>
                </div>
            @endif

            <!-- Mobile: Manual Entry floating button -->
            <div class="absolute bottom-6 left-0 right-0 px-6 md:hidden z-20">
                <button type="button" @click="showMobileSearch = true" class="w-full py-3.5 px-5 rounded-2xl bg-blue-600/90 hover:bg-blue-600 active:scale-95 backdrop-blur-xl border border-blue-400/40 text-white font-extrabold text-sm shadow-2xl flex items-center justify-center gap-2 cursor-pointer transition-all">
                    <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Manual Search</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Right Panel: Sidebar (Desktop & Mobile Drawer) -->
    <div 
        x-cloak
        :class="{
            'fixed inset-0 z-50 flex flex-col bg-slate-950/95 backdrop-blur-2xl p-4 sm:p-6': showMobileSearch,
            'hidden md:flex flex-col w-full md:w-96 bg-slate-900 border-l border-white/5 shrink-0 z-20': !showMobileSearch
        }"
    >
        <!-- Mobile Drawer Header -->
        <div class="flex items-center justify-between p-3 pb-4 border-b border-white/10 md:hidden mb-2">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <h3 class="text-base font-extrabold text-white">Manual Entry Search</h3>
            </div>
            <button type="button" @click="showMobileSearch = false" class="p-2 rounded-xl bg-white/10 text-slate-300 hover:text-white transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Manual Search -->
        <div class="p-4 sm:p-6 border-b border-white/5 bg-slate-900/80 rounded-2xl md:rounded-none mb-4 md:mb-0">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Manual Entry</h3>
            <div class="relative">
                <input wire:model.live.debounce.300ms="manualSearchQuery" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors" placeholder="Search name or email...">
                <svg class="w-5 h-5 text-slate-500 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            
                @if(strlen($manualSearchQuery) > 2)
                    <div class="absolute left-0 right-0 top-full mt-2 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl overflow-hidden z-50 max-h-64 overflow-y-auto">
                        @forelse($searchResults as $result)
                            <button wire:click="manualCheckIn('{{ $result->uuid }}')" @click="showMobileSearch = false" class="w-full text-left px-4 py-3 hover:bg-slate-700 transition-colors border-b border-slate-700/50 last:border-0 cursor-pointer">
                                <div class="font-medium text-white">{{ $result->full_name }}</div>
                                <div class="text-xs text-slate-400 flex flex-wrap justify-between items-center mt-1 gap-1">
                                    <span>{{ $result->email }} @if($result->phone) • {{ $result->phone }} @endif</span>
                                    <span class="uppercase font-bold text-blue-400">{{ is_object($result->access_role) ? $result->access_role->label() : ucfirst($result->access_role) }}</span>
                                </div>
                            </button>
                        @empty
                            <div class="px-4 py-3 text-sm text-slate-400">No attendees found.</div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Scans -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-slate-900/50">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Recent Scans</h3>
            <div class="space-y-3">
                @forelse($recentScans as $activity)
                    @php
                        $actResultStr = is_object($activity->scan_result) ? $activity->scan_result->value : (string)$activity->scan_result;
                        $att = $activity->attendee;
                        $gateObj = $activity->gate;
                    @endphp
                    <div class="p-4 rounded-xl border border-white/5 bg-slate-950/60 flex items-start space-x-3 hover:border-blue-500/30 transition-all">
                        <div class="mt-1 w-2.5 h-2.5 rounded-full shrink-0 {{ $actResultStr === 'granted' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]' : 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.8)]' }}"></div>
                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-extrabold text-white truncate">{{ $att->full_name ?? 'Unknown Attendee' }}</p>
                                <span class="text-[11px] font-mono text-slate-400 shrink-0">{{ $activity->scanned_at ? $activity->scanned_at->format('H:i:s') : '' }}</span>
                            </div>

                            <!-- Gate Scanned -->
                            <div class="flex items-center gap-1.5 text-xs text-blue-400 font-semibold">
                                <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span class="truncate">Gate: <strong>{{ $gateObj->name ?? ($gate->name ?? 'Main Gate') }}</strong></span>
                            </div>

                            <div class="pt-0.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $actResultStr === 'granted' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                    {{ ucfirst($actResultStr) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 text-sm">
                        Waiting for first scan...
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endif
</div>

<style>
    @keyframes scan {
        0%, 100% { top: 0%; opacity: 0; }
        10%, 90% { opacity: 1; }
        50% { top: 100%; }
    }
    .animate-scan {
        animation: scan 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
    @keyframes bounce-in {
        0% { transform: scale(0.8); opacity: 0; }
        60% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-bounce-in {
        animation: bounce-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }
    #qr-reader-video video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    #qr-reader-video {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
    }
</style>

@script
<script>
    console.log('Scanner UI initialized');

    let liveHtml5QrCode = null;
    let lastScannedCode = '';
    let lastScannedTimestamp = 0;

    window.initLiveCameraScanner = function() {
        const overlay = document.getElementById('camera-status-overlay');
        const statusText = document.getElementById('camera-status-text');

        if (overlay && statusText) {
            overlay.classList.remove('hidden');
            statusText.innerText = "Requesting device camera permission...";
        }

        if (typeof Html5Qrcode === 'undefined') {
            console.warn('Html5Qrcode library loading...');
            setTimeout(initLiveCameraScanner, 500);
            return;
        }

        const videoContainer = document.getElementById('qr-reader-video');
        if (!videoContainer) return;

        if (liveHtml5QrCode) {
            try {
                liveHtml5QrCode.stop().catch(() => {}).then(() => {
                    liveHtml5QrCode = null;
                    startCamera();
                });
                return;
            } catch(e) {
                liveHtml5QrCode = null;
            }
        }

        startCamera();
    };

    function startCamera() {
        const overlay = document.getElementById('camera-status-overlay');
        const statusText = document.getElementById('camera-status-text');

        try {
            liveHtml5QrCode = new Html5Qrcode("qr-reader-video");
            const config = {
                fps: 15,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            // Attempt 1: Start rear/environment camera (main phone camera)
            liveHtml5QrCode.start(
                { facingMode: "environment" },
                config,
                onLiveQrScanSuccess,
                onLiveQrScanError
            ).then(() => {
                if (overlay) overlay.classList.add('hidden');
                console.log("📷 Live Environment Camera stream active!");
            }).catch(err => {
                console.warn("Environment camera unavailable, falling back to default camera:", err);
                // Attempt 2: Fallback to user/default camera
                liveHtml5QrCode.start(
                    { facingMode: "user" },
                    config,
                    onLiveQrScanSuccess,
                    onLiveQrScanError
                ).then(() => {
                    if (overlay) overlay.classList.add('hidden');
                    console.log("📷 Live User Camera stream active!");
                }).catch(err2 => {
                    console.error("Camera access error:", err2);
                    if (overlay && statusText) {
                        overlay.classList.remove('hidden');
                        if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                            statusText.innerText = "⚠️ Phone camera access requires HTTPS or localhost. Please use HTTPS or Upload QR Image.";
                        } else {
                            statusText.innerText = "⚠️ Camera permission denied or camera in use. Please grant camera permission.";
                        }
                    }
                });
            });
        } catch(err) {
            console.error("Error setting up Html5Qrcode:", err);
        }
    }

    function onLiveQrScanSuccess(decodedText) {
        const now = Date.now();
        if (decodedText === lastScannedCode && (now - lastScannedTimestamp) < 3000) {
            return;
        }
        lastScannedCode = decodedText;
        lastScannedTimestamp = now;

        console.log("📷 Live Camera QR Scan Success:", decodedText);
        $wire.processQrScan(decodedText);
    }

    function onLiveQrScanError(err) {
        // Silent frame scan callback
    }

    $wire.on('resume-scanning', () => {
        console.log('Resumed scanning');
    });

    window.handleQrFileUpload = function(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (typeof Html5Qrcode !== 'undefined') {
            try {
                const html5QrCodeFile = new Html5Qrcode("qr-reader-file-temp");
                html5QrCodeFile.scanFile(file, true)
                    .then(decodedText => {
                        console.log("Scanned QR file code via Html5Qrcode:", decodedText);
                        $wire.processQrScan(decodedText);
                    })
                    .catch(err => {
                        decodeWithJsQr(file);
                    });
            } catch(e) {
                decodeWithJsQr(file);
            }
        } else {
            decodeWithJsQr(file);
        }
    };

    function decodeWithJsQr(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = img.width;
                canvas.height = img.height;
                context.drawImage(img, 0, 0, img.width, img.height);
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                
                if (typeof jsQR !== 'undefined') {
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    if (code && code.data) {
                        console.log("Scanned QR file code via jsQR:", code.data);
                        $wire.processQrScan(code.data);
                        return;
                    }
                }
                alert("Could not detect a valid QR code in this image. Please ensure the QR code image is clear.");
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    document.addEventListener('keydown', (e) => {
        if(e.key === 'Enter' && e.ctrlKey) {
            $wire.processQrScan('test-uuid-or-qr-content');
        }
    });

    // Auto-initialize camera stream
    setTimeout(initLiveCameraScanner, 600);
</script>
@endscript

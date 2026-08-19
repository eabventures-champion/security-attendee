<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Official Virtual ID Card — {{ $card->full_name }}</title>
    
    <!-- Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.11/html-to-image.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        .id-card-glow {
            box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.22), 0 0 0 1px rgba(16, 185, 129, 0.15);
        }
        .hologram-strip {
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.02) 50%, rgba(255,255,255,0.15) 100%);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-white font-sans antialiased py-8 px-4">

    <div class="max-w-2xl mx-auto w-full space-y-6">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-black tracking-wider uppercase">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Official Verified Digital Credential
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Virtual Membership ID</h1>
            <p class="text-xs sm:text-sm text-slate-400">Issued by {{ $card->organization ? $card->organization->name : 'Federation of African Law Students (FALAS)' }}</p>
        </div>

        <!-- Live Scan Verification Feedback Banner -->
        @if($card->status === 'active')
            <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-emerald-500/15 via-emerald-500/10 to-transparent border-2 border-emerald-500/40 text-emerald-400 flex items-center justify-between gap-3 shadow-lg shadow-emerald-500/10 animate-fadeIn">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-black text-lg shrink-0 border border-emerald-500/30">
                        ✓
                    </div>
                    <div class="space-y-0.5 text-left">
                        <div class="font-black text-sm text-emerald-300 tracking-tight flex flex-wrap items-center gap-1.5">
                            <span>AUTHENTICATED &amp; VALID</span>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-wider border border-emerald-500/30">Active Status</span>
                        </div>
                        <p class="text-xs text-slate-300 font-medium">
                            Official digital pass for <strong>{{ $card->full_name }}</strong> (<span class="font-mono text-emerald-300 font-bold">{{ $card->member_id_number }}</span>).
                        </p>
                    </div>
                </div>
                <div class="hidden sm:block text-right shrink-0 font-mono text-[10px] text-slate-400">
                    <div>Security Token</div>
                    <div class="text-emerald-400 font-bold">{{ $card->qr_token }}</div>
                </div>
            </div>
        @elseif($card->status === 'suspended')
            <div class="p-4 rounded-2xl bg-amber-500/15 border-2 border-amber-500/30 text-amber-400 flex items-center gap-3 shadow-lg">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center font-black text-lg shrink-0">
                    ⚠
                </div>
                <div class="space-y-0.5 text-left">
                    <div class="font-black text-sm text-amber-300 tracking-tight">CREDENTIAL TEMPORARILY SUSPENDED</div>
                    <p class="text-xs text-slate-300">This member card is currently suspended. Please contact FALAS administration.</p>
                </div>
            </div>
        @else
            <div class="p-4 rounded-2xl bg-rose-500/15 border-2 border-rose-500/30 text-rose-400 flex items-center gap-3 shadow-lg">
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-300 flex items-center justify-center font-black text-lg shrink-0">
                    ✕
                </div>
                <div class="space-y-0.5 text-left">
                    <div class="font-black text-sm text-rose-300 tracking-tight">EXPIRED OR INACTIVE CREDENTIAL</div>
                    <p class="text-xs text-slate-300">This virtual ID card is inactive or has expired.</p>
                </div>
            </div>
        @endif

        <!-- ID Card Container with Main Logo Green & White Theme + Pinch of Yellow -->
        <div id="virtual-id-card-element" class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-[#064e3b] via-[#033b2c] to-[#02241b] border-2 border-emerald-400/40 ring-1 ring-white/20 id-card-glow p-5 sm:p-6 space-y-5 text-white">
            
            <!-- Background Institution Logo / Law Watermark -->
            <div class="absolute inset-0 opacity-[0.06] pointer-events-none flex items-center justify-center overflow-hidden p-6 select-none">
                @if($card->institution_logo_url)
                    <img src="{{ $card->institution_logo_url }}" crossorigin="anonymous" alt="Institution Emblem" class="w-72 h-72 object-contain filter grayscale contrast-150 invert">
                @else
                    <svg class="w-72 h-72 text-white" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18l-8 4m8-4l8 4M4 7l-2 6h8L8 7m8 0l-2 6h8l-2-6M6 21h12"/>
                    </svg>
                @endif
            </div>

            <!-- Card Top Header: Centered Organization & Subtitle with Left & Right Balanced Logos -->
            <div class="flex items-center justify-between border-b border-white/20 pb-3.5 relative z-10 gap-3">
                <!-- 1. Main Logo (Left) -->
                <div class="w-12 h-12 sm:w-14 sm:h-14 min-w-[48px] min-h-[48px] max-w-[56px] max-h-[56px] rounded-xl bg-white p-1 flex items-center justify-center shrink-0 shadow-md border-2 border-white/90 overflow-hidden">
                    @if($card->main_logo_url)
                        <img src="{{ $card->main_logo_url }}" crossorigin="anonymous" alt="Main Logo" class="max-w-full max-h-full object-contain" title="Main Logo">
                    @else
                        <div class="w-full h-full rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black text-base shrink-0 shadow-inner">
                            🏛️
                        </div>
                    @endif
                </div>

                <!-- Center: Organization Title & Subtitle in Green & White with Pinch of Yellow -->
                <div class="text-center flex-1 min-w-0 space-y-0.5 px-1">
                    @php
                        $rawOrgName = $card->organization ? $card->organization->name : 'Federation of African Law Students';
                        if (stripos($rawOrgName, 'IDENTITY CARD') !== false) {
                            $orgTitle = trim(preg_replace('/[-–—]?\s*identity\s*card/i', '', $rawOrgName));
                            $hasIdCardSuffix = true;
                        } else {
                            $orgTitle = $rawOrgName;
                            $hasIdCardSuffix = false;
                        }
                    @endphp
                    <div class="text-xs sm:text-sm font-black uppercase tracking-wider text-white font-sans leading-tight">
                        {{ $orgTitle }}
                    </div>
                    @if($hasIdCardSuffix)
                        <div class="text-[10px] sm:text-[11px] font-black uppercase tracking-widest text-yellow-300 font-sans leading-tight">
                            IDENTITY CARD
                        </div>
                    @endif
                    <div class="text-[8.5px] sm:text-[9.5px] font-extrabold uppercase tracking-[0.18em] text-emerald-100/80 font-sans pt-0.5">
                        Official Student Pass
                    </div>
                </div>

                <!-- 2. Association Logo (Right) -->
                <div class="w-12 h-12 sm:w-14 sm:h-14 min-w-[48px] min-h-[48px] max-w-[56px] max-h-[56px] rounded-xl bg-white p-1 flex items-center justify-center shrink-0 shadow-md border-2 border-white/90 overflow-hidden">
                    @if($card->association_logo_url)
                        <img src="{{ $card->association_logo_url }}" crossorigin="anonymous" alt="Association Logo" class="max-w-full max-h-full object-contain" title="Association Logo">
                    @else
                        <div class="w-full h-full rounded-lg bg-emerald-700 flex items-center justify-center text-white font-black text-xs">
                            FALAS
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Body: Framed Portrait & Credentials Spotlight -->
            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-5 relative z-10 py-1">
                
                <!-- Framed Photo Container in White-Emerald-Gold Gradient -->
                <div class="relative shrink-0">
                    <div class="w-28 h-36 sm:w-32 sm:h-40 rounded-2xl p-1 bg-gradient-to-b from-white via-emerald-300 to-yellow-400/80 shadow-xl shadow-black/30">
                        <div class="w-full h-full rounded-[14px] overflow-hidden bg-slate-900 flex items-center justify-center relative">
                            @if($card->photo_url)
                                <img src="{{ $card->photo_url }}" crossorigin="anonymous" alt="{{ $card->full_name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-b from-slate-800 to-slate-950 text-slate-500">
                                    <svg class="w-14 h-14 text-slate-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                    <span class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mt-1">Photo on File</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Verified Holographic Badge Indicator -->
                    <div class="absolute -bottom-1 -right-1 p-1 rounded-full bg-emerald-500 border-2 border-white text-white shadow">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>

                <!-- Member Credentials Info -->
                <div class="flex-1 text-center sm:text-left space-y-2.5 w-full">
                    <div class="space-y-1.5">
                        <div>
                            <span class="text-[9.5px] font-extrabold uppercase tracking-widest text-emerald-200/90 block">Cardholder Name</span>
                            <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-snug">{{ $card->full_name }}</h2>
                        </div>
                        
                        <!-- Member ID Number Badge under Name in Crisp White/Green -->
                        <div class="flex items-center justify-center sm:justify-start">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg bg-white/15 border border-white/30 text-white font-mono text-[11px] font-bold shadow-sm tracking-wider leading-none text-center">
                                {{ $card->member_id_number }}
                            </span>
                        </div>

                        <!-- Institution under Name -->
                        @if($card->institution)
                            <div class="text-[11px] sm:text-xs text-white font-medium leading-snug pt-0.5">
                                {{ $card->institution }}
                            </div>
                        @endif

                    </div>

                    <!-- 3-Pill Tier: Admission | Country Director / Executive Role | Completion -->
                    <div class="flex items-stretch gap-1.5 pt-2">
                        <!-- 1. Admission -->
                        <div class="bg-white/10 border border-white/20 rounded-xl py-1.5 px-2.5 text-center shrink-0 min-w-[65px] sm:min-w-[75px] flex flex-col items-center justify-center gap-1">
                            <span class="text-[7.5px] sm:text-[8px] text-emerald-200 block font-extrabold uppercase tracking-wider leading-none text-center">Admission</span>
                            <span class="font-bold text-white font-mono text-[10.5px] sm:text-[11px] block leading-none text-center">{{ $card->admission_year ?: 'N/A' }}</span>
                        </div>

                        <!-- 2. Role / Designation (In Between - Pinch of Yellow Accent) -->
                        <div class="flex-1 rounded-xl py-1.5 px-2.5 text-center flex items-center justify-center shadow-sm min-w-0 {{ $card->isExecutive() ? 'bg-gradient-to-b from-yellow-500/20 via-yellow-500/10 to-transparent border border-yellow-400/60 text-yellow-300 shadow-yellow-500/10' : 'bg-white/15 border border-white/30 text-white' }}">
                            <div class="inline-flex items-center justify-center gap-1.5 leading-none">
                                @if($card->isExecutive())
                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-yellow-400 shrink-0 inline-block" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="font-black text-[9px] sm:text-[10px] uppercase tracking-tight whitespace-nowrap leading-none text-yellow-300">
                                        {{ !empty($card->position) ? strtoupper($card->position) : 'EXECUTIVE' }}
                                    </span>
                                @else
                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-white/80 shrink-0 inline-block" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-black text-[9px] sm:text-[10px] uppercase tracking-tight whitespace-nowrap leading-none text-white">
                                        MEMBER
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- 3. Completion -->
                        <div class="bg-white/10 border border-white/20 rounded-xl py-1.5 px-2.5 text-center shrink-0 min-w-[65px] sm:min-w-[75px] flex flex-col items-center justify-center gap-1">
                            <span class="text-[7.5px] sm:text-[8px] text-emerald-200 block font-extrabold uppercase tracking-wider leading-none text-center">Completion</span>
                            <span class="font-bold text-white font-mono text-[10.5px] sm:text-[11px] block leading-none text-center">{{ $card->completion_year ?: 'Present' }}</span>
                        </div>
                    </div>

                    @if(!empty($card->custom_fields))
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            @foreach($card->custom_fields as $cfKey => $cfVal)
                                @if(!empty($cfVal))
                                    <div class="bg-white/10 border border-white/20 rounded-xl p-2 text-center sm:text-left">
                                        <span class="text-[9px] text-emerald-200 block font-bold uppercase tracking-wider truncate">{{ ucwords(str_replace('_', ' ', $cfKey)) }}</span>
                                        <span class="font-bold text-white truncate block text-[11px]">{{ $cfVal }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Bottom Bar with QR Code Verification -->
            <div class="pt-3.5 border-t border-white/20 flex items-center justify-between gap-3 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="p-1 rounded-xl bg-white shadow shrink-0 flex items-center justify-center">
                        <img src="{{ $card->qr_code_url }}" crossorigin="anonymous" alt="QR Verification" class="w-12 h-12 sm:w-14 sm:h-14 rounded block object-contain">
                    </div>
                    <div class="text-left space-y-0.5">
                        <div class="text-[10.5px] font-black uppercase tracking-wide text-white flex items-center gap-1.5 whitespace-nowrap">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Digitally Verified</span>
                        </div>
                        <div class="text-[9.5px] text-emerald-100 font-mono tracking-tight whitespace-nowrap">Token: {{ $card->qr_token }}</div>
                        <div class="text-[8.5px] text-emerald-200/80 whitespace-nowrap">Scan code with camera to verify</div>
                    </div>
                </div>

                <div class="shrink-0 flex items-center justify-center">
                    <div class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider whitespace-nowrap leading-none {{ $card->status === 'active' ? 'bg-white/20 text-white border border-white/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }}">
                        <span class="w-2 h-2 rounded-full shrink-0 inline-block {{ $card->status === 'active' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                        <span class="whitespace-nowrap leading-none flex items-center">{{ strtoupper($card->status) }} ID</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Action Download Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button id="download-png-btn" onclick="downloadCardAsImage()" class="flex-1 py-3.5 px-6 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Save / Download ID Card (PNG)</span>
            </button>
            <button onclick="window.print()" class="py-3.5 px-5 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-slate-200 font-bold text-sm transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print</span>
            </button>
        </div>

    </div>

    <!-- iOS / Safari Long-Press Save Modal Fallback -->
    <div id="ios-save-modal" class="hidden fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md p-4 flex items-center justify-center animate-fadeIn">
        <div class="bg-slate-900 border border-emerald-500/30 rounded-3xl p-5 sm:p-6 max-w-md w-full text-center space-y-4 shadow-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-black uppercase">
                📱 iPhone / iPad Save Guide
            </div>
            <h3 class="text-lg font-black text-white">Save ID Card to Photos</h3>
            <div class="rounded-2xl overflow-hidden border border-white/10 shadow-lg max-h-[50vh] flex items-center justify-center bg-slate-950">
                <img id="ios-modal-img" src="" alt="Virtual ID Card" class="max-w-full max-h-[50vh] object-contain rounded-xl select-all">
            </div>
            <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs text-left space-y-1">
                <div class="font-extrabold flex items-center gap-1.5">
                    <span>👆</span> <span>How to save on iPhone:</span>
                </div>
                <p class="text-slate-300 text-[11px] leading-relaxed">
                    <strong>Press and hold (long-press)</strong> the card image above, then tap <strong>"Save to Photos"</strong> (or "Share" &rarr; "Save Image").
                </p>
            </div>
            <button onclick="closeIOSModal()" class="w-full py-3 px-4 rounded-xl bg-white/10 hover:bg-white/15 text-white font-bold text-xs transition cursor-pointer">
                Done / Close
            </button>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-xs text-slate-600 pt-8">
        &copy; {{ date('Y') }} {{ $card->organization ? $card->organization->name : config('app.name') }}. All rights reserved.
    </div>

    <script>
        function dataURLtoBlob(dataUrl) {
            const arr = dataUrl.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new Blob([u8arr], { type: mime });
        }

        function showIOSPreviewModal(dataUrl) {
            const modal = document.getElementById('ios-save-modal');
            const img = document.getElementById('ios-modal-img');
            img.src = dataUrl;
            modal.classList.remove('hidden');
        }

        function closeIOSModal() {
            const modal = document.getElementById('ios-save-modal');
            modal.classList.add('hidden');
        }

        async function downloadCardAsImage() {
            const btn = document.getElementById('download-png-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span>Rendering Image...</span>';
            btn.disabled = true;

            const element = document.getElementById('virtual-id-card-element');
            const filename = 'Virtual_ID_{{ Str::slug($card->full_name) }}_{{ $card->member_id_number }}.png';

            const triggerDownload = async (dataUrl) => {
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
                
                try {
                    const blob = dataURLtoBlob(dataUrl);
                    const file = new File([blob], filename, { type: 'image/png' });

                    // 1. Web Share API (Primary for iPhone / iOS to directly save to Photos or Files)
                    if (navigator.canShare && navigator.canShare({ files: [file] })) {
                        try {
                            await navigator.share({
                                files: [file],
                                title: 'Official Virtual ID Card',
                                text: 'Virtual ID Card - {{ $card->full_name }}'
                            });
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                            return;
                        } catch (shareErr) {
                            if (shareErr.name === 'AbortError') {
                                // User simply cancelled share dialog
                                btn.innerHTML = originalText;
                                btn.disabled = false;
                                return;
                            }
                            console.warn('Share API error, falling back:', shareErr);
                        }
                    }

                    // 2. Standard Blob Download for Android / Desktop
                    const blobUrl = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.download = filename;
                    link.href = blobUrl;
                    document.body.appendChild(link);
                    link.click();

                    setTimeout(() => {
                        document.body.removeChild(link);
                        URL.revokeObjectURL(blobUrl);
                    }, 100);

                    // 3. If on iOS and browser did not open download/share, show preview modal
                    if (isIOS) {
                        showIOSPreviewModal(dataUrl);
                    }
                } catch (e) {
                    console.error('Download handling error:', e);
                    if (isIOS) {
                        showIOSPreviewModal(dataUrl);
                    } else {
                        const link = document.createElement('a');
                        link.download = filename;
                        link.href = dataUrl;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                } finally {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            };

            const runDownload = () => {
                if (window.htmlToImage) {
                    window.htmlToImage.toPng(element, {
                        pixelRatio: 3,
                        cacheBust: true,
                    }).then(triggerDownload).catch(err => {
                        console.warn('htmlToImage error, falling back to html2canvas:', err);
                        fallbackHtml2Canvas();
                    });
                } else {
                    fallbackHtml2Canvas();
                }
            };

            function fallbackHtml2Canvas() {
                if (window.html2canvas) {
                    html2canvas(element, {
                        scale: 3,
                        useCORS: true,
                        allowTaint: false,
                        backgroundColor: null,
                        logging: false,
                    }).then(canvas => {
                        triggerDownload(canvas.toDataURL('image/png'));
                    }).catch(err => {
                        console.error('Snapshot error:', err);
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        alert('Could not generate image snapshot automatically. Please use the Print button to save as PDF or image.');
                    });
                } else {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    alert('Snapshot engine could not load. Please use the Print button.');
                }
            }

            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(runDownload).catch(runDownload);
            } else {
                runDownload();
            }
        }
    </script>
</body>
</html>

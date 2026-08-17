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
            box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
        }
        .hologram-strip {
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.02) 50%, rgba(255,255,255,0.15) 100%);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-blue-500 selection:text-white font-sans antialiased py-8 px-4">

    <div class="max-w-xl mx-auto w-full space-y-6">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-black tracking-wider uppercase">
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

        <!-- ID Card Container for Snapshot -->
        <div id="virtual-id-card-element" class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border-2 border-blue-500/40 id-card-glow p-6 sm:p-7 space-y-6">
            
            <!-- Background Institution Logo / Law Watermark -->
            <div class="absolute inset-0 opacity-[0.07] pointer-events-none flex items-center justify-center overflow-hidden p-6 select-none">
                @if($card->institution_logo_url)
                    <img src="{{ $card->institution_logo_url }}" alt="Institution Emblem" class="w-80 h-80 object-contain filter grayscale contrast-150">
                @else
                    <svg class="w-80 h-80 text-white" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18l-8 4m8-4l8 4M4 7l-2 6h8L8 7m8 0l-2 6h8l-2-6M6 21h12"/>
                    </svg>
                @endif
            </div>

            <!-- Card Top Header with Institution Logo -->
            <div class="flex items-center justify-between border-b border-white/10 pb-4 relative z-10 gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    @if($card->institution_logo_url)
                        <img src="{{ $card->institution_logo_url }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl bg-white/5 p-1 border border-white/10 shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center font-black text-sm shrink-0">
                            🏛️
                        </div>
                    @endif
                    <div class="space-y-0.5 min-w-0">
                        <div class="text-xs font-black uppercase tracking-wider text-blue-400 font-sans leading-tight">
                            Federation of African Law Students (FALAS)
                        </div>
                        <div class="text-xs font-bold text-orange-400 tracking-wide font-sans">
                            {{ $card->institution ?: 'University of Ghana, School of Law' }}
                        </div>
                    </div>
                </div>
                <div class="shrink-0 text-right">
                    <span class="inline-block px-2.5 py-1 rounded-lg bg-blue-500/20 border border-blue-500/30 text-blue-300 font-mono text-xs font-black tracking-wider whitespace-nowrap">
                        {{ $card->member_id_number }}
                    </span>
                </div>
            </div>

            <!-- Main Body: Photo & Credentials -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 relative z-10">
                
                <!-- Photo Container / Silhouette Fallback -->
                <div class="w-28 h-36 sm:w-32 sm:h-40 rounded-2xl border-2 border-blue-400/50 bg-slate-800/80 overflow-hidden flex items-center justify-center shrink-0 shadow-lg relative group">
                    @if($card->photo_url)
                        <img src="{{ $card->photo_url }}" alt="{{ $card->full_name }}" class="w-full h-full object-cover">
                    @else
                        <!-- Elegant Silhouette Fallback -->
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-b from-slate-800 to-slate-950 text-slate-500">
                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-500 mt-1">Photo on File</span>
                        </div>
                    @endif
                    <!-- Verified Holographic Badge Indicator -->
                    <div class="absolute bottom-1 right-1 p-1 rounded-full bg-blue-600 text-white shadow">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>

                <!-- Member Credentials Info -->
                <div class="flex-1 text-center sm:text-left space-y-3 w-full">
                    <div>
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Cardholder Name</div>
                        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $card->full_name }}</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-white/5 border border-white/5 rounded-xl p-2">
                            <span class="text-[10px] text-slate-400 block font-semibold uppercase">Admission</span>
                            <span class="font-bold text-slate-200">{{ $card->admission_year ?: 'N/A' }}</span>
                        </div>
                        <div class="bg-white/5 border border-white/5 rounded-xl p-2">
                            <span class="text-[10px] text-slate-400 block font-semibold uppercase">Completion</span>
                            <span class="font-bold text-slate-200">{{ $card->completion_year ?: 'Present' }}</span>
                        </div>
                    </div>

                    @if(!empty($card->custom_fields))
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            @foreach($card->custom_fields as $cfKey => $cfVal)
                                @if(!empty($cfVal))
                                    <div class="bg-white/5 border border-white/5 rounded-xl p-2">
                                        <span class="text-[10px] text-slate-400 block font-semibold uppercase">{{ ucwords(str_replace('_', ' ', $cfKey)) }}</span>
                                        <span class="font-bold text-slate-200 truncate block">{{ $cfVal }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Bottom Bar with QR Code Verification -->
            <div class="pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 relative z-10">
                <div class="flex items-center gap-3.5">
                    <div class="p-1.5 rounded-xl bg-white shadow-md shrink-0">
                        <img src="{{ $card->qr_code_url }}" alt="QR Verification" class="w-16 h-16 rounded-lg">
                    </div>
                    <div class="space-y-0.5 text-left">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Digitally Verified ID
                        </span>
                        <div class="text-[10px] text-slate-400 font-mono">Token: {{ $card->qr_token }}</div>
                        <div class="text-[9px] text-slate-500">Scan code with any camera to verify validity.</div>
                    </div>
                </div>

                <div class="text-right shrink-0">
                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $card->status === 'active' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }}">
                        {{ strtoupper($card->status) }}
                    </span>
                </div>
            </div>

        </div>

        <!-- Action Download Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button id="download-png-btn" onclick="downloadCardAsImage()" class="flex-1 py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Save / Download ID Card (PNG)</span>
            </button>
            <button onclick="window.print()" class="py-3.5 px-5 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-slate-200 font-bold text-sm transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print</span>
            </button>
        </div>

    </div>

    <!-- Footer -->
    <div class="text-center text-xs text-slate-600 pt-8">
        &copy; {{ date('Y') }} {{ $card->organization ? $card->organization->name : config('app.name') }}. All rights reserved.
    </div>

    <script>
        function downloadCardAsImage() {
            const btn = document.getElementById('download-png-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span>Rendering Image...</span>';
            btn.disabled = true;

            const element = document.getElementById('virtual-id-card-element');
            html2canvas(element, {
                scale: 3,
                useCORS: true,
                backgroundColor: '#090d16'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Virtual_ID_{{ Str::slug($card->full_name) }}_{{ $card->member_id_number }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(err => {
                console.error(err);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Could not generate image snapshot. Please use Print button.');
            });
        }
    </script>
</body>
</html>

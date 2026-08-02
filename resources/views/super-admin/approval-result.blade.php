<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Approval — AttendFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-950 flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-md bg-slate-900 border border-white/10 rounded-3xl p-8 shadow-2xl backdrop-blur-xl text-center space-y-6 animate-fadeIn">
        @if($success)
            <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/20">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 tracking-wider uppercase">SUPER ADMIN APPROVED</span>
                <h1 class="text-2xl font-extrabold text-white mt-3">Organization Activated!</h1>
                <p class="text-slate-400 text-sm mt-2 leading-relaxed">{{ $message }}</p>
            </div>
            
            <div class="p-4 rounded-2xl bg-slate-950 border border-white/5 text-left space-y-2 text-xs">
                <div class="flex justify-between border-b border-white/5 pb-2">
                    <span class="text-slate-500 font-medium">Organization</span>
                    <span class="font-bold text-blue-400">{{ $organization->name ?? 'Organization' }}</span>
                </div>
                <div class="flex justify-between border-b border-white/5 pb-2">
                    <span class="text-slate-500 font-medium">Admin Name</span>
                    <span class="font-bold text-white">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Admin Email</span>
                    <span class="font-bold text-slate-300">{{ $user->email }}</span>
                </div>
            </div>

            <a href="{{ route('login') }}" class="block w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold rounded-xl shadow-lg shadow-blue-500/25 transition-all text-sm">
                Proceed to Sign In
            </a>
        @else
            <div class="w-16 h-16 bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-2xl flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Approval Failed</h1>
            <p class="text-slate-400 text-sm">{{ $message }}</p>
            <a href="{{ route('home') }}" class="block w-full py-3 px-4 bg-white/10 text-white font-bold rounded-xl text-sm hover:bg-white/20 transition-all">
                Return to Home
            </a>
        @endif
    </div>
</body>
</html>

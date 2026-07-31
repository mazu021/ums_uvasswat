<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - UVAS Swat ERP Portal</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen w-full flex items-center justify-center bg-slate-50 p-4 relative overflow-y-auto select-none">

    <!-- Background Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#00a257]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#2e2e7f]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-6 sm:p-8 border border-slate-200/80 relative z-10 my-auto overflow-hidden">
        <!-- Top Branding Stripe -->
        <div class="h-2 bg-gradient-to-r from-[#2e2e7f] to-[#00a257] absolute top-0 left-0 right-0"></div>

        <div class="text-center mb-6 pt-2">
            <div class="w-16 h-16 bg-[#2e2e7f]/10 text-[#2e2e7f] rounded-2xl flex items-center justify-center mx-auto mb-3 border border-[#2e2e7f]/15 shadow-sm">
                <i class="fa-solid fa-key text-2xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-[#2e2e7f]">Reset Password</h2>
            <p class="text-xs text-slate-500 mt-1">University of Veterinary & Animal Sciences, Swat</p>
        </div>

        @if(session('status'))
            <div class="mb-4 p-3 bg-emerald-50 border-l-4 border-[#00a257] rounded-r-xl text-[#00a257] text-xs font-semibold flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-[#00a257] text-sm"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl text-rose-700 text-xs font-semibold flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Institutional Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-envelope text-sm"></i>
                    </div>
                    <input type="email" name="email" required placeholder="admin@uvasswat.edu.pk" 
                        class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#2e2e7f] focus:ring-4 focus:ring-[#2e2e7f]/10 focus:outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">New Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <input type="password" name="password" required minlength="6" placeholder="Enter your new password" 
                        class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#2e2e7f] focus:ring-4 focus:ring-[#2e2e7f]/10 focus:outline-none transition">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-amber-400 hover:bg-amber-500 text-slate-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                <span>UPDATE PASSWORD & LOGIN</span>
                <i class="fa-solid fa-check text-xs"></i>
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
            <a href="{{ route('login') }}" class="text-[#2e2e7f] hover:underline flex items-center">
                <i class="fa-solid fa-arrow-left me-1.5"></i> Back to Login
            </a>
            <a href="mailto:cms@uvasswat.edu.pk" class="text-[#00a257] hover:underline">
                Contact IT Helpdesk
            </a>
        </div>
    </div>

</body>
</html>

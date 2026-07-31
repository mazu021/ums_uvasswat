<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UVAS Swat ERP Portal</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen w-full flex items-center justify-center bg-slate-50 p-3 sm:p-6 md:p-8 relative overflow-y-auto md:overflow-hidden select-none" x-data="{ email: 'admin@uvasswat.edu.pk', password: '', showPassword: false, showForgotModal: false, resetEmail: '', resetSubmitted: false }">

    <!-- Subtle Page Background Decorative Grid & Glows -->
    <div class="absolute inset-0 bg-[radial-gradient(#2e2e7f_1px,transparent_1px)] [background-size:24px_24px] opacity-[0.04] pointer-events-none"></div>
    <div class="absolute -top-32 -left-32 w-72 sm:w-96 h-72 sm:h-96 bg-[#00a257]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-72 sm:w-96 h-72 sm:h-96 bg-[#2e2e7f]/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Responsive Card Container -->
    <div class="w-full max-w-4xl bg-white rounded-2xl sm:rounded-3xl shadow-[0_20px_50px_-15px_rgba(46,46,127,0.15)] overflow-hidden grid grid-cols-1 md:grid-cols-12 border border-slate-200/80 relative z-10 my-auto md:max-h-[calc(100vh-2rem)]">

        <!-- Left Brand Banner (#2e2e7f and #00a257) -->
        <div class="md:col-span-6 bg-[#2e2e7f] text-white p-5 sm:p-6 md:p-8 flex flex-col justify-between items-center text-center relative overflow-hidden">
            <!-- Green Brand Glow (#00a257) -->
            <div class="absolute top-0 right-0 w-48 sm:w-64 h-48 sm:h-64 bg-[#00a257]/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-48 sm:w-64 h-48 sm:h-64 bg-[#00a257]/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Tagline Badge (White Text) -->
            <div class="relative z-10 w-full flex items-center justify-center">
                <span class="px-3.5 py-1 bg-white/10 backdrop-blur-md rounded-full text-[10px] sm:text-xs font-bold tracking-wider uppercase text-white border border-white/20 shadow-sm flex items-center gap-1.5 sm:gap-2">
                    <span class="w-1.5 sm:w-2 h-1.5 sm:h-2 rounded-full bg-[#00a257] animate-pulse"></span>
                    University Management System
                </span>
            </div>

            <!-- Middle Logo & Brand Text -->
            <div class="relative z-10 my-3 sm:my-4 space-y-2.5 sm:space-y-4 flex flex-col items-center w-full">
                <!-- White Logo Card Container -->
                <div class="bg-white p-2.5 sm:p-3 rounded-xl sm:rounded-2xl shadow-xl border-2 border-white transform hover:scale-105 transition duration-300 flex items-center justify-center w-28 h-24 sm:w-36 sm:h-32">
                    <img src="{{ asset('images/uvas_logo.png') }}" alt="UVAS Swat Logo" class="max-w-full max-h-full object-contain">
                </div>
                
                <div class="space-y-1 w-full px-2">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white tracking-wide">UVAS SWAT</h1>
                    <div class="w-10 sm:w-12 h-1 bg-[#00a257] rounded-full mx-auto"></div>
                    <p class="text-[10px] sm:text-xs text-slate-200 font-medium pt-0.5 whitespace-nowrap overflow-hidden text-ellipsis">
                        The University of Veterinary & Animal Sciences, Swat
                    </p>
                </div>
            </div>

            <!-- Bottom Information -->
            <div class="relative z-10 w-full pt-2.5 sm:pt-3 border-t border-white/15">
                <p class="text-[10px] sm:text-[11px] text-slate-300 font-medium">
                    © Directorate of IT, UVAS Swat
                </p>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="md:col-span-6 p-5 sm:p-6 md:p-8 bg-white flex flex-col justify-between">
            <div>
                <!-- Form Header -->
                <div class="mb-4 sm:mb-5">
                    <div class="inline-flex items-center justify-center w-9 sm:w-11 h-9 sm:h-11 bg-[#2e2e7f]/10 text-[#2e2e7f] rounded-xl mb-2 sm:mb-2.5 shadow-sm border border-[#2e2e7f]/15">
                        <i class="fa-solid fa-user-lock text-base sm:text-xl"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-[#2e2e7f] tracking-tight">LOGIN</h2>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 font-medium">Welcome to University Management System</p>
                </div>

                <!-- Session Alert / Status Messages -->
                @if(session('status'))
                    <div class="mb-3 sm:mb-4 p-2.5 sm:p-3 bg-emerald-50 border-l-4 border-[#00a257] rounded-r-xl text-[#00a257] text-xs font-semibold flex items-center space-x-2 shadow-sm">
                        <i class="fa-solid fa-circle-check text-[#00a257] text-sm"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-3 sm:mb-4 p-2.5 sm:p-3 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl text-rose-700 text-xs font-semibold flex items-center space-x-2 shadow-sm">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-3 sm:space-y-3.5">
                    @csrf
                    
                    <!-- Email Field -->
                    <div>
                        <label class="block text-[10px] sm:text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-regular fa-envelope text-xs sm:text-sm"></i>
                            </div>
                            <input type="email" name="email" x-model="email" required placeholder="youremail@gmail.com" 
                                class="w-full pl-9 sm:pl-10 pr-3 sm:pr-3.5 py-2 sm:py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#2e2e7f] focus:ring-4 focus:ring-[#2e2e7f]/10 focus:outline-none transition duration-200">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label class="block text-[10px] sm:text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock text-xs sm:text-sm"></i>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required placeholder="••••••••••••" 
                                class="w-full pl-9 sm:pl-10 pr-9 sm:pr-10 py-2 sm:py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#2e2e7f] focus:ring-4 focus:ring-[#2e2e7f]/10 focus:outline-none transition duration-200">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 sm:pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                                <i class="fa-solid text-xs sm:text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex items-center text-[11px] sm:text-xs font-medium text-slate-600 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 sm:w-4 sm:h-4 rounded border-slate-300 text-[#2e2e7f] focus:ring-[#2e2e7f]/30 me-2 transition">
                            <span class="group-hover:text-slate-900 transition">Keep me logged in</span>
                        </label>
                    </div>

                    <!-- Action Buttons: Sign In (Blue) and Forgot Password (Yellow) -->
                    <div class="space-y-2 pt-1">
                        <button type="submit" class="w-full py-2.5 sm:py-3 bg-[#2e2e7f] hover:bg-[#232363] text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-lg shadow-[#2e2e7f]/25 hover:shadow-[#2e2e7f]/40 hover:-translate-y-0.5 active:translate-y-0 transition duration-200 flex items-center justify-center space-x-2">
                            <span>SIGN IN</span>
                            <i class="fa-solid fa-arrow-right-to-bracket text-xs sm:text-sm"></i>
                        </button>
                        <button type="button" @click="showForgotModal = true; resetEmail = email; resetSubmitted = false" class="w-full py-2.5 sm:py-3 bg-amber-400 hover:bg-amber-500 text-slate-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition duration-200 flex items-center justify-center space-x-2">
                            <span>FORGOT PASSWORD?</span>
                            <i class="fa-solid fa-key text-xs sm:text-sm"></i>
                        </button>
                    </div>
                </form>

                <!-- Footer Account Helper Link -->
                <div class="mt-4 sm:mt-5 text-center text-[11px] sm:text-xs text-slate-500 font-medium">
                    <a href="https://www.linkedin.com/in/mazu021/" target="_blank" rel="noopener noreferrer" class="hover:text-[#2e2e7f] hover:underline transition">
                        &copy; UMS PORTAL The University of Veterinary and Animal Sciences, Swat (UVAS Swat) - Directorate of IT
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- FORGOT PASSWORD INTERACTIVE MODAL -->
    <div x-show="showForgotModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showForgotModal = false" class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-6 sm:p-8 border border-slate-100 relative overflow-hidden">
            <!-- Top Color Stripe -->
            <div class="h-2 bg-gradient-to-r from-[#2e2e7f] to-[#00a257] absolute top-0 left-0 right-0"></div>

            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center font-bold">
                        <i class="fa-solid fa-key text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-[#2e2e7f]">Reset Password</h3>
                        <p class="text-xs text-slate-500">UVAS Swat Self-Service Portal</p>
                    </div>
                </div>
                <button type="button" @click="showForgotModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Instant Password Reset Form -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-4 pt-2">
                @csrf
                <p class="text-xs text-slate-600 leading-relaxed">
                    Enter your registered email address and choose a new password below to reset instantly.
                </p>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Registered Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </div>
                        <input type="email" name="email" x-model="resetEmail" required placeholder="admin@uvasswat.edu.pk" 
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#2e2e7f] focus:ring-4 focus:ring-[#2e2e7f]/10 focus:outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" name="password" required minlength="6" placeholder="Enter new password" 
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#2e2e7f] focus:ring-4 focus:ring-[#2e2e7f]/10 focus:outline-none transition">
                    </div>
                </div>

                <div class="pt-2 flex items-center space-x-2">
                    <button type="submit" class="flex-1 py-3 bg-[#2e2e7f] hover:bg-[#232363] text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                        <span>UPDATE PASSWORD</span>
                        <i class="fa-solid fa-check text-xs"></i>
                    </button>
                    <button type="button" @click="showForgotModal = false" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm rounded-xl transition">
                        Cancel
                    </button>
                </div>
            </form>

        </div>
    </div>


</body>
</html>

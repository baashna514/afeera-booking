<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - Online Bus Ticketing Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-white min-h-screen flex items-center justify-center p-4 sm:p-6 font-sans text-slate-800 selection:bg-[#da1705] selection:text-white">

    <div class="max-w-[440px] w-full my-auto space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center mb-1">
                <div class="w-16 h-16 rounded-2xl bg-red-50 border-2 border-[#da1705] p-3 shadow-lg shadow-red-500/15 flex items-center justify-center">
                    @include('components.application-logo', ['class' => 'w-full h-full'])
                </div>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                Online<span class="text-[#da1705]">Ticketing</span> <span class="font-bold text-slate-600 text-2xl">Portal</span>
            </h1>
            <p class="text-xs font-semibold text-slate-500">Enterprise Bus Reservation & Counter Terminal</p>
        </div>

        <!-- Login Card Container (Clean White with Red Accent) -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/80 p-8 border border-slate-200 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-xl font-black text-slate-900">Sign In to Your Account</h2>
                <p class="text-xs font-medium text-slate-500 mt-1">Please enter your authorized login credentials</p>
            </div>

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-[#da1705] text-xs font-bold flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-sm shrink-0"></i>
                    <div>
                        <span class="block font-black mb-0.5">Authentication Failed</span>
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('status'))
                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-blue-600"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1.5">
                        EMAIL ADDRESS *
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="user@ticketing.com"
                               class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-300 text-slate-900 font-semibold rounded-xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:border-[#da1705] focus:outline-none transition placeholder-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1.5">
                        PASSWORD *
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" id="passwordInput" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-10 py-2.5 text-sm bg-slate-50 border border-slate-300 text-slate-900 font-semibold rounded-xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:border-[#da1705] focus:outline-none transition placeholder-slate-400">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition">
                            <i id="eyeIcon" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-[#da1705] focus:ring-[#da1705]">
                        <span class="text-xs text-slate-600 font-medium">Remember me on this device</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-[#da1705] hover:bg-[#b91204] active:scale-[0.99] text-white font-bold rounded-xl shadow-lg shadow-red-900/25 transition text-sm flex items-center justify-center gap-2 cursor-pointer mt-2">
                    <i class="fa-solid fa-right-to-bracket text-sm"></i>
                    <span>Sign In</span>
                </button>
            </form>
        </div>

        <div class="text-center text-xs font-semibold text-slate-400">
            Online Bus Ticketing System &copy; {{ date('Y') }} • All rights reserved
        </div>
    </div>

    <script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>

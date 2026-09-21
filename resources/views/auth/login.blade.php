<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Ticketing Software</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-[#070d1d] min-h-screen flex items-center justify-center p-4 font-sans text-slate-100">

    <div class="max-w-md w-full space-y-6">
        <!-- Logo & Header matching reference UI -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center mb-1">
                @include('components.application-logo', ['class' => 'w-16 h-16'])
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">
                Online<span class="text-[#da1705]">Ticketing</span> <span class="font-normal text-slate-400 text-2xl">Portal</span>
            </h1>
            <p class="text-xs font-medium text-slate-400">Enterprise Bus Ticketing & Terminal Management</p>
        </div>

        <!-- Login Card Container -->
        <div class="bg-[#10182b] rounded-3xl shadow-2xl p-8 border border-[#1e2d4d] space-y-6">
            <div>
                <h2 class="text-xl font-bold text-white">Sign In to Your Account</h2>
                <p class="text-xs text-slate-400 mt-1">Please enter your authorized login credentials below</p>
            </div>

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs font-semibold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-800 text-emerald-300 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        EMAIL ADDRESS
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="admin@ticketing.com"
                               class="w-full pl-11 pr-4 py-3 text-sm bg-[#eef2f6] border border-slate-300 text-slate-900 font-semibold rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:outline-none transition placeholder-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        PASSWORD
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required
                               placeholder="••••••••"
                               class="w-full pl-11 pr-4 py-3 text-sm bg-[#eef2f6] border border-slate-300 text-slate-900 font-semibold rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:outline-none transition placeholder-slate-400">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-[#da1705] focus:ring-[#da1705]">
                        <span class="text-xs text-slate-300 font-medium">Remember me on this device</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-[#da1705] hover:bg-[#b91204] text-white font-bold rounded-2xl shadow-xl shadow-red-900/40 transition text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    <span>Sign In</span>
                </button>
            </form>

            <!-- Quick Demo Accounts -->
            <div class="pt-3 border-t border-[#1e2d4d] space-y-2">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center">Quick Demo Accounts</span>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <button type="button" onclick="fillLogin('admin@ticketing.com', 'password123')" class="py-2.5 px-2 rounded-xl bg-[#0a101f] border border-slate-700 hover:border-[#da1705] text-slate-200 text-center font-bold text-[11px] transition">
                        <i class="fa-solid fa-shield-halved text-rose-500 mb-0.5 block"></i> Admin
                    </button>
                    <button type="button" onclick="fillLogin('owner@ticketing.com', 'password123')" class="py-2.5 px-2 rounded-xl bg-[#0a101f] border border-slate-700 hover:border-[#da1705] text-slate-200 text-center font-bold text-[11px] transition">
                        <i class="fa-solid fa-crown text-amber-400 mb-0.5 block"></i> Owner
                    </button>
                    <button type="button" onclick="fillLogin('user@ticketing.com', 'password123')" class="py-2.5 px-2 rounded-xl bg-[#0a101f] border border-slate-700 hover:border-[#da1705] text-slate-200 text-center font-bold text-[11px] transition">
                        <i class="fa-solid fa-desktop text-[#da1705] mb-0.5 block"></i> Counter
                    </button>
                </div>
            </div>

            <div class="pt-4 border-t border-[#1e2d4d] text-center">
                <p class="text-xs text-slate-400">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-bold text-[#da1705] hover:underline transition">Create an account</a>
                </p>
            </div>
        </div>

        <script>
        function fillLogin(email, password) {
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('input[name="password"]').value = password;
        }
        </script>

        <div class="text-center text-xs text-slate-400">
            <a href="{{ url('/') }}" class="hover:text-white transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Back to Homepage
            </a>
        </div>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Online Ticketing Software</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-[#070d1d] min-h-screen flex items-center justify-center p-4 font-sans text-slate-100">

    <div class="max-w-md w-full space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center mb-1">
                @include('components.application-logo', ['class' => 'w-16 h-16'])
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">
                Online<span class="text-[#da1705]">Ticketing</span> <span class="font-normal text-slate-400 text-2xl">Portal</span>
            </h1>
            <p class="text-xs font-medium text-slate-400">Create your account to access the system</p>
        </div>

        <!-- Register Card Container -->
        <div class="bg-[#10182b] rounded-3xl shadow-2xl p-8 border border-[#1e2d4d] space-y-5">
            <div>
                <h2 class="text-xl font-bold text-white">Create New Account</h2>
                <p class="text-xs text-slate-400 mt-1">Enter your registration details below</p>
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

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Account Role / Portal *
                    </label>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="user" {{ old('role', 'user') === 'user' ? 'checked' : '' }} class="peer sr-only">
                            <div class="p-3 rounded-2xl bg-[#0a101f] border border-slate-700 peer-checked:border-[#da1705] peer-checked:bg-red-950/30 text-center transition">
                                <i class="fa-solid fa-desktop text-base text-[#da1705] mb-1 block"></i>
                                <span class="font-bold text-white block text-[11px]">Counter Staff</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="super_admin" {{ old('role') === 'super_admin' ? 'checked' : '' }} class="peer sr-only">
                            <div class="p-3 rounded-2xl bg-[#0a101f] border border-slate-700 peer-checked:border-[#da1705] peer-checked:bg-red-950/30 text-center transition">
                                <i class="fa-solid fa-shield-halved text-base text-rose-500 mb-1 block"></i>
                                <span class="font-bold text-white block text-[11px]">Super Admin</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="owner" {{ old('role') === 'owner' ? 'checked' : '' }} class="peer sr-only">
                            <div class="p-3 rounded-2xl bg-[#0a101f] border border-slate-700 peer-checked:border-[#da1705] peer-checked:bg-red-950/30 text-center transition">
                                <i class="fa-solid fa-crown text-base text-amber-400 mb-1 block"></i>
                                <span class="font-bold text-white block text-[11px]">Owner</span>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Full Name *
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-regular fa-user"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Ali Ahmad"
                               class="w-full pl-11 pr-4 py-3 text-sm bg-[#eef2f6] border border-slate-300 text-slate-900 font-semibold rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:outline-none transition placeholder-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Email Address *
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="user@ticketing.com"
                               class="w-full pl-11 pr-4 py-3 text-sm bg-[#eef2f6] border border-slate-300 text-slate-900 font-semibold rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:outline-none transition placeholder-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Password *
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full pl-11 pr-4 py-3 text-sm bg-[#eef2f6] border border-slate-300 text-slate-900 font-semibold rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:outline-none transition placeholder-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Confirm Password *
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password_confirmation" required placeholder="••••••••"
                               class="w-full pl-11 pr-4 py-3 text-sm bg-[#eef2f6] border border-slate-300 text-slate-900 font-semibold rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:outline-none transition placeholder-slate-400">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-[#da1705] hover:bg-[#b91204] text-white font-bold rounded-2xl shadow-xl shadow-red-900/40 transition text-sm flex items-center justify-center gap-2 mt-2">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>Register Account</span>
                </button>
            </form>

            <div class="pt-4 border-t border-[#1e2d4d] text-center">
                <p class="text-xs text-slate-400">
                    Already registered?
                    <a href="{{ route('login') }}" class="font-bold text-[#da1705] hover:underline transition">Sign in here</a>
                </p>
            </div>
        </div>

        <div class="text-center text-xs text-slate-400">
            <a href="{{ url('/') }}" class="hover:text-white transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Back to Homepage
            </a>
        </div>
    </div>

</body>
</html>

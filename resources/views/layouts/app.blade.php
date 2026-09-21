<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Online Ticketing Software') }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#ffe1e1',
                            500: '#da1705',
                            600: '#c21203',
                            700: '#9e0d02',
                            800: '#7e0e04',
                            900: '#4a0802',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <nav class="bg-slate-900 text-white border-b border-slate-800 sticky top-0 z-40 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-6">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-black text-xl text-white tracking-tight">
                        <div class="w-10 h-10 rounded-xl bg-[#da1705] text-white flex items-center justify-center text-lg shadow-md shadow-red-900/40">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <span>Online<span class="text-[#da1705]">Ticketing</span></span>
                    </a>

                    @auth
                        <div class="hidden md:flex items-center gap-1.5">
                            @if(auth()->user()->role === 'owner')
                                <a href="{{ route('owner.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('owner.dashboard') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-crown mr-1 text-amber-400"></i> Owner Portal
                                </a>
                                <a href="{{ route('owner.companies.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('owner.companies.*') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-building mr-1"></i> Companies
                                </a>
                            @elseif(auth()->user()->role === 'super_admin')
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-shield-halved mr-1 text-rose-400"></i> Admin
                                </a>
                                <a href="{{ route('admin.cities.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.cities.*') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-city mr-1"></i> Cities
                                </a>
                                <a href="{{ route('admin.vehicles.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.vehicles.*') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-bus mr-1"></i> Buses Fleet
                                </a>
                                <a href="{{ route('admin.vehicle-service-types.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.vehicle-service-types.*') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-chair mr-1"></i> Service Classes
                                </a>
                                <a href="{{ route('admin.routes.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.routes.*') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-route mr-1"></i> Routes
                                </a>
                                <a href="{{ route('admin.schedules.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.schedules.*') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-clock mr-1"></i> Schedules
                                </a>
                                <a href="{{ route('admin.fares.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.fares.*') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-money-bill-wave mr-1"></i> Fares
                                </a>
                                <a href="{{ route('admin.expense-types.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.expense-types.*') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-receipt mr-1"></i> Expense Types
                                </a>
                            @else
                                <a href="{{ route('user.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('user.dashboard') || request()->routeIs('dashboard') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-desktop mr-1"></i> Counter Terminal
                                </a>
                                <a href="{{ route('user.booking.history') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('user.booking.history') ? 'bg-[#da1705] text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }} transition">
                                    <i class="fa-solid fa-receipt mr-1"></i> Issued Tickets
                                </a>
                            @endif
                        </div>
                    @endauth
                </div>

                <!-- Right Side User Menu -->
                <div class="flex items-center gap-4">
                    @auth
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <div class="text-sm font-bold text-white">{{ auth()->user()->name }}</div>
                                <div class="text-xs font-semibold uppercase tracking-wider">
                                    @if(auth()->user()->role === 'owner')
                                        <span class="text-amber-400"><i class="fa-solid fa-crown text-[10px]"></i> Owner</span>
                                    @elseif(auth()->user()->role === 'super_admin')
                                        <span class="text-[#da1705] font-bold"><i class="fa-solid fa-shield text-[10px]"></i> Super Admin</span>
                                    @else
                                        <span class="text-emerald-400"><i class="fa-solid fa-desktop text-[10px]"></i> Counter Staff</span>
                                    @endif
                                </div>
                            </div>
                            
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-[#da1705] hover:bg-slate-800 transition" title="Log Out">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-base"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-300 hover:text-white transition">Log In</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-[#da1705] hover:bg-[#b91204] text-white text-sm font-bold rounded-xl shadow-sm transition">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
        @if(session('success'))
            <div class="p-4 mb-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Body Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @isset($header)
            <div class="mb-6">
                {{ $header }}
            </div>
        @endisset

        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 py-4 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} Online Ticketing Software. All rights reserved.
    </footer>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Ticketing Software</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-white min-h-screen text-slate-800 flex flex-col justify-between font-sans">

    <!-- Header / Navbar -->
    <header class="max-w-7xl mx-auto w-full px-6 py-5 flex items-center justify-between border-b border-slate-100 bg-white">
        <div class="flex items-center gap-3">
            @include('components.application-logo', ['class' => 'w-10 h-10'])
            <div>
                <span class="text-xl font-black tracking-tight text-slate-900 block">Online<span class="text-[#da1705]">Ticketing</span></span>
                <span class="text-[10px] text-slate-500 font-semibold tracking-wider uppercase block">Bus Transport Management System</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if (Route::has('login'))
                @auth
                    @if(auth()->user()->role === 'owner')
                        <a href="{{ route('owner.dashboard') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white font-bold text-sm rounded-xl shadow-lg shadow-red-900/20 transition">
                            Owner Dashboard <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    @elseif(auth()->user()->role === 'super_admin')
                        <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white font-bold text-sm rounded-xl shadow-lg shadow-red-900/20 transition">
                            Super Admin Dashboard <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white font-bold text-sm rounded-xl shadow-lg shadow-red-900/20 transition">
                            Counter Terminal <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-bold text-slate-700 hover:text-[#da1705] transition">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white font-bold text-sm rounded-xl shadow-lg shadow-red-900/20 transition">
                            Register
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </header>

    <!-- Hero Section -->
    <main class="max-w-5xl mx-auto px-6 py-16 text-center flex-1 flex flex-col items-center justify-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-50 text-[#da1705] border border-red-200 text-xs font-bold uppercase tracking-wider mb-6">
            <i class="fa-solid fa-sparkles"></i> 3-Tier Enterprise Bus Ticketing Solution
        </div>

        <h1 class="text-4xl sm:text-6xl font-black text-slate-900 tracking-tight leading-tight max-w-3xl">
            Streamlined <span class="text-[#da1705]">Bus Ticketing</span> & Terminal Management
        </h1>

        <p class="text-slate-600 text-base sm:text-lg max-w-2xl mt-5 leading-relaxed">
            Enterprise multi-role ticketing platform for Bus Companies, Super Admin Configuration, and Counter Ticket Issuance Terminals.
        </p>

        <!-- 3 Dashboards Feature Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12 w-full text-left">
            <!-- Owner Dashboard Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:border-[#da1705] transition duration-300">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-4 font-bold border border-amber-200">
                    <i class="fa-solid fa-crown"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Owner Portal</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Create, edit, delete, and manage all registered bus company entities.
                </p>
                <div class="mt-4 pt-4 border-t border-slate-100 text-[11px] text-amber-600 font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-check mr-1"></i> Companies Management
                </div>
            </div>

            <!-- Super Admin Dashboard Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:border-[#da1705] transition duration-300">
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-[#da1705] flex items-center justify-center text-xl mb-4 font-bold border border-red-200">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Super Admin Control</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Manage cities, bus fleet, service classes (Gold/Business/Economy), routes, intermediate stops, schedules, and fares.
                </p>
                <div class="mt-4 pt-4 border-t border-slate-100 text-[11px] text-[#da1705] font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-check mr-1"></i> Complete Control Panel
                </div>
            </div>

            <!-- Counter User Terminal Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:border-[#da1705] transition duration-300">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4 font-bold border border-emerald-200">
                    <i class="fa-solid fa-desktop"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Counter Staff Terminal</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Interactive seat map terminal for counter operators to sell, issue, print, and manage passenger tickets.
                </p>
                <div class="mt-4 pt-4 border-t border-slate-100 text-[11px] text-emerald-600 font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-check mr-1"></i> Interactive Ticket Counter
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-wrap gap-4 justify-center">
            @guest
                <a href="{{ route('register') }}" class="px-8 py-3.5 bg-[#da1705] hover:bg-[#b91204] text-white font-bold rounded-2xl shadow-xl shadow-red-900/20 transition text-sm">
                    Get Started Now
                </a>
                <a href="{{ route('login') }}" class="px-8 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-2xl border border-slate-200 transition text-sm">
                    Sign In to Portal
                </a>
            @endguest
        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-7xl mx-auto w-full px-6 py-6 text-center text-xs text-slate-400 border-t border-slate-100 bg-white">
        &copy; {{ date('Y') }} Online Ticketing Software. Built with Laravel.
    </footer>

</body>
</html>

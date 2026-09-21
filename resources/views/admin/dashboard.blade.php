@extends('layouts.app', ['title' => 'Super Admin Dashboard - Online Ticketing Software'])

@section('content')
<div class="space-y-6">

    <!-- Top Banner -->
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#da1705]/20 text-[#da1705] border border-[#da1705]/30 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-shield-halved"></i> Super Admin Control Panel
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-3">
                    <span>Welcome, {{ auth()->user()->name }}!</span>
                </h1>
                <p class="text-xs text-slate-300 mt-1 max-w-xl">
                    Full platform control. Manage cities, bus fleet vehicles, service classes, routes, schedules, fares, companies, and monitor overall ticket sales.
                </p>
            </div>
            <div>
                <span class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white/10 text-white border border-white/10">
                    <i class="fa-solid fa-circle text-emerald-400 text-[9px] mr-1.5 animate-pulse"></i> System Active
                </span>
            </div>
        </div>
    </div>

    <!-- Main Platform Overview Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4">
        <a href="{{ route('admin.cities.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md hover:border-[#da1705] transition group">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center text-lg font-bold mb-2 group-hover:bg-[#da1705] group-hover:text-white transition">
                <i class="fa-solid fa-city"></i>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Cities</span>
            <span class="text-xl font-black text-slate-900">{{ $totalCities ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.vehicles.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md hover:border-[#da1705] transition group">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center text-lg font-bold mb-2 group-hover:bg-[#da1705] group-hover:text-white transition">
                <i class="fa-solid fa-bus"></i>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Bus Fleet</span>
            <span class="text-xl font-black text-slate-900">{{ $totalVehicles ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.vehicle-service-types.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md hover:border-[#da1705] transition group">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center text-lg font-bold mb-2 group-hover:bg-[#da1705] group-hover:text-white transition">
                <i class="fa-solid fa-chair"></i>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Service Classes</span>
            <span class="text-xl font-black text-slate-900">{{ $totalVehicleTypes ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.routes.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md hover:border-[#da1705] transition group">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center text-lg font-bold mb-2 group-hover:bg-[#da1705] group-hover:text-white transition">
                <i class="fa-solid fa-route"></i>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Routes</span>
            <span class="text-xl font-black text-slate-900">{{ $totalRoutes ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.schedules.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md hover:border-[#da1705] transition group">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center text-lg font-bold mb-2 group-hover:bg-[#da1705] group-hover:text-white transition">
                <i class="fa-solid fa-clock"></i>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Schedules</span>
            <span class="text-xl font-black text-[#da1705]">{{ $totalSchedules ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.fares.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md hover:border-emerald-300 transition group">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold mb-2 group-hover:bg-emerald-600 group-hover:text-white transition">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Fares Configured</span>
            <span class="text-xl font-black text-emerald-600">{{ $totalFares ?? 0 }}</span>
        </a>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center text-lg font-bold mb-2">
                <i class="fa-solid fa-building"></i>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Companies</span>
            <span class="text-xl font-black text-slate-900">{{ $totalCompanies ?? 0 }}</span>
        </div>
    </div>

    <!-- Management Quick Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-city"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Cities & Stations</h3>
                    <p class="text-xs text-slate-500">Boarding & dropping points</p>
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.cities.index') }}" class="flex-1 text-center py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">View List</a>
                <a href="{{ route('admin.cities.create') }}" class="flex-1 text-center py-2 bg-[#da1705] hover:bg-[#b91204] text-white font-bold text-xs rounded-xl shadow-xs transition">+ Add City</a>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-bus"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Bus Fleet Vehicles</h3>
                    <p class="text-xs text-slate-500">B-102, ABC-123 registration</p>
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.vehicles.index') }}" class="flex-1 text-center py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">Fleet List</a>
                <a href="{{ route('admin.vehicles.create') }}" class="flex-1 text-center py-2 bg-[#da1705] hover:bg-[#b91204] text-white font-bold text-xs rounded-xl shadow-xs transition">+ Add Bus</a>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-chair"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Service Classes</h3>
                    <p class="text-xs text-slate-500">Gold (18), Business (37), Economy (45)</p>
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.vehicle-service-types.index') }}" class="flex-1 text-center py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">View List</a>
                <a href="{{ route('admin.vehicle-service-types.create') }}" class="flex-1 text-center py-2 bg-[#da1705] hover:bg-[#b91204] text-white font-bold text-xs rounded-xl shadow-xs transition">+ Add Type</a>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-route"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Routes & Stops</h3>
                    <p class="text-xs text-slate-500">Routes with ordered stops</p>
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.routes.index') }}" class="flex-1 text-center py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">View List</a>
                <a href="{{ route('admin.routes.create') }}" class="flex-1 text-center py-2 bg-[#da1705] hover:bg-[#b91204] text-white font-bold text-xs rounded-xl shadow-xs transition">+ Add Route</a>
            </div>
        </div>
    </div>

    <!-- Registered Companies Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-building text-[#da1705]"></i>
                    <span>Recently Registered Bus Companies</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Bus companies registered on the platform.</p>
            </div>
        </div>

        @if($recentCompanies->isEmpty())
            <div class="py-8 text-center text-slate-400 text-sm">
                No companies registered yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">ID</th>
                            <th class="px-4 py-3">Company Name</th>
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Owner Name</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentCompanies as $comp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono text-xs text-slate-400">#{{ $comp->id }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $comp->name }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $comp->code ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-slate-700">{{ $comp->owner->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ $comp->phone ?? '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $comp->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $comp->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

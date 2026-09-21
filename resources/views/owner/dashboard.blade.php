@extends('layouts.app', ['title' => 'Owner Dashboard - Online Ticketing Software'])

@section('content')
<div class="space-y-6">

    <!-- Top Banner -->
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-crown"></i> Platform Owner Portal
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-3">
                    <span>Welcome, {{ auth()->user()->name }}!</span>
                </h1>
                <p class="text-xs text-slate-300 mt-1 max-w-xl">
                    Here you can create, edit, delete, and oversee all registered bus company entities.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('owner.companies.create') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-lg shadow-red-900/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Add New Company
                </a>
                <a href="{{ route('owner.companies.index') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-xl border border-white/10 transition flex items-center gap-2">
                    <i class="fa-solid fa-list"></i> View All Companies
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-[#da1705] flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-building"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Companies</span>
                <span class="text-2xl font-black text-slate-900">{{ $totalCompanies ?? 0 }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Active Status</span>
                <span class="text-2xl font-black text-emerald-600">Online</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Your Role</span>
                <span class="text-2xl font-black text-amber-600">Owner</span>
            </div>
        </div>
    </div>

    <!-- Quick Companies Listing Section -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-city text-[#da1705]"></i>
                    <span>Your Companies</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Manage and organize all bus companies under your ownership.</p>
            </div>
            <a href="{{ route('owner.companies.create') }}" class="px-4 py-2 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-md transition inline-flex items-center gap-1.5 w-fit">
                <i class="fa-solid fa-plus"></i> Add Company
            </a>
        </div>

        @if($companies->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#da1705] flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-building-circle-exclamation"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">No Companies Found</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    You haven't added any company yet. Click the button below to add your first company.
                </p>
                <div class="mt-4">
                    <a href="{{ route('owner.companies.create') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-md transition inline-flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Add Your First Company
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">Company</th>
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Address</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($companies as $company)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3.5 font-bold text-slate-900 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-red-50 text-[#da1705] flex items-center justify-center text-sm font-bold">
                                        <i class="fa-solid fa-building"></i>
                                    </div>
                                    <span>{{ $company->name }}</span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-xs text-slate-500">
                                    {{ $company->code ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-600">
                                    {{ $company->phone ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-600 max-w-xs truncate">
                                    {{ $company->address ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $company->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $company->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right space-x-2">
                                    <a href="{{ route('owner.companies.edit', $company) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <form action="{{ route('owner.companies.destroy', $company) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this company?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-[#da1705] text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can"></i> Delete
                                        </button>
                                    </form>
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

@extends('layouts.app', ['title' => 'Companies - Owner Portal'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-building text-[#da1705]"></i>
                <span>Manage Companies</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Add, edit, or delete companies under your ownership.</p>
        </div>
        <a href="{{ route('owner.companies.create') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-lg shadow-red-900/30 transition inline-flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Add New Company
        </a>
    </div>

    <!-- Companies Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        @if($companies->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#da1705] flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-building-circle-exclamation"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">No Companies Found</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    You haven't added any company yet.
                </p>
                <div class="mt-4">
                    <a href="{{ route('owner.companies.create') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-md transition inline-flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Add Company
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">ID</th>
                            <th class="px-4 py-3">Company Name</th>
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
                                <td class="px-4 py-3.5 font-mono text-xs text-slate-400">
                                    #{{ $company->id }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-900 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 text-[#da1705] flex items-center justify-center text-xs font-bold">
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

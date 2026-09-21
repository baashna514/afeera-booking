@extends('layouts.app', ['title' => 'Manage Cities - Super Admin'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-city text-rose-600"></i>
                <span>Manage Cities</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Add, edit, and manage departure and arrival cities across the ticketing platform.</p>
        </div>
        <div>
            <a href="{{ route('admin.cities.create') }}" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-rose-600/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add New City
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        @if($cities->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-building-flag"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">No Cities Found</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Click 'Add New City' above to register the first city in the system.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">#</th>
                            <th class="px-4 py-3">City Name</th>
                            <th class="px-4 py-3">Province / Region</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($cities as $city)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono text-xs text-slate-400">#{{ $city->id }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $city->name }}</td>
                                <td class="px-4 py-3 text-xs text-slate-600">{{ $city->province ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $city->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ $city->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.cities.edit', $city) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this city?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i> Delete
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

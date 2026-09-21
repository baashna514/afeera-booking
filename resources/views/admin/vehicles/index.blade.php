@extends('layouts.app', ['title' => 'Manage Bus Vehicles - Super Admin'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-bus-simple text-[#da1705]"></i>
                <span>Bus Fleet Vehicles</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Register specific bus vehicles (e.g. Bus B-102, ABC-123) and link them to operating companies and service classes.</p>
        </div>
        <div>
            <a href="{{ route('admin.vehicles.create') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-lg shadow-red-900/20 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add New Bus Vehicle
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        @if($vehicles->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#da1705] flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-bus"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">No Bus Vehicles Registered</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Register physical buses with bus numbers and registration plates to assign them to time schedules.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">#</th>
                            <th class="px-4 py-3">Bus Number</th>
                            <th class="px-4 py-3">Registration Plate</th>
                            <th class="px-4 py-3">Operating Company</th>
                            <th class="px-4 py-3">Service Class</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($vehicles as $vehicle)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono text-xs text-slate-400">#{{ $vehicle->id }}</td>
                                <td class="px-4 py-3 font-black text-slate-900 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg bg-red-50 text-[#da1705] flex items-center justify-center font-bold text-xs">
                                        <i class="fa-solid fa-bus"></i>
                                    </span>
                                    <span>{{ $vehicle->bus_number }}</span>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-800">{{ $vehicle->registration_number }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-slate-700">{{ $vehicle->company->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-bold">
                                        {{ $vehicle->vehicleServiceType->name ?? '—' }} ({{ $vehicle->vehicleServiceType->total_seats ?? 0 }} Seats)
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                        {{ $vehicle->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($vehicle->status === 'maintenance' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                        {{ $vehicle->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this bus vehicle?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-[#da1705] text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
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

@extends('layouts.app', ['title' => 'Manage Time Schedules - Super Admin'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-clock text-[#da1705]"></i>
                <span>Manage Time Schedules</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Set departure time slots, assigned bus fleet, and journey durations for each route and service class.</p>
        </div>
        <div>
            <a href="{{ route('admin.schedules.create') }}" class="px-5 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-lg shadow-red-900/20 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add Time Schedule
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        @if($schedules->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#da1705] flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">No Time Schedules Found</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Create departure time slots for routes so ticket counters can sell tickets for specific times.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">#</th>
                            <th class="px-4 py-3">Route</th>
                            <th class="px-4 py-3">Vehicle Class & Assigned Bus</th>
                            <th class="px-4 py-3">Departure Time</th>
                            <th class="px-4 py-3">Duration & Est. Arrival</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($schedules as $schedule)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono text-xs text-slate-400">#{{ $schedule->id }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900">
                                    {{ $schedule->route->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <div class="font-bold text-slate-900">{{ $schedule->vehicleServiceType->name ?? '—' }} ({{ $schedule->vehicleServiceType->total_seats ?? 0 }} Seats)</div>
                                    @if($schedule->vehicle)
                                        <div class="text-[11px] font-mono text-[#da1705] mt-0.5">
                                            <i class="fa-solid fa-bus text-[10px]"></i> Bus {{ $schedule->vehicle->bus_number }} ({{ $schedule->vehicle->registration_number }})
                                        </div>
                                    @else
                                        <div class="text-[10px] text-slate-400 italic">No specific bus assigned</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-900 text-sm">
                                    <i class="fa-solid fa-clock text-emerald-600 text-xs mr-1"></i>
                                    {{ \Carbon\Carbon::parse($schedule->departure_time)->format('h:i A') }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-600">
                                    @if($schedule->duration_minutes)
                                        <div class="font-bold text-slate-800">
                                            {{ floor($schedule->duration_minutes / 60) }}h {{ $schedule->duration_minutes % 60 }}m
                                        </div>
                                    @endif
                                    <div class="text-[11px] text-slate-500">
                                        Arrival: {{ $schedule->arrival_time ? \Carbon\Carbon::parse($schedule->arrival_time)->format('h:i A') : '—' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $schedule->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ $schedule->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this schedule?')">
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

@extends('layouts.app', ['title' => 'Edit Time Schedule - Super Admin'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-[#da1705]"></i>
                <span>Edit Time Schedule</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Update departure slot details and assigned bus vehicle.</p>
        </div>
        <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="route_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Select Route *</label>
                <select name="route_id" id="route_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 bg-white">
                    <option value="">-- Select Route --</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id', $schedule->route_id) == $route->id ? 'selected' : '' }}>
                            {{ $route->name }} ({{ $route->originCity->name ?? '' }} → {{ $route->destinationCity->name ?? '' }})
                        </option>
                    @endforeach
                </select>
                @error('route_id')
                    <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="vehicle_service_type_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Vehicle Service Class *</label>
                <select name="vehicle_service_type_id" id="vehicle_service_type_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 bg-white">
                    <option value="">-- Select Vehicle Class --</option>
                    @foreach($vehicleTypes as $type)
                        <option value="{{ $type->id }}" {{ old('vehicle_service_type_id', $schedule->vehicle_service_type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} ({{ $type->total_seats }} Seats)
                        </option>
                    @endforeach
                </select>
                @error('vehicle_service_type_id')
                    <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="vehicle_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Assign Specific Bus Vehicle (Optional)</label>
                <select name="vehicle_id" id="vehicle_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 bg-white">
                    <option value="">-- Unassigned (Generic Class Schedule) --</option>
                    @foreach($vehicles as $veh)
                        <option value="{{ $veh->id }}" {{ old('vehicle_id', $schedule->vehicle_id) == $veh->id ? 'selected' : '' }}>
                            Bus {{ $veh->bus_number }} (Reg: {{ $veh->registration_number }}) - {{ $veh->company->name ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_id')
                    <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="departure_time" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Departure Time *</label>
                    <input type="time" name="departure_time" id="departure_time" value="{{ old('departure_time', \Carbon\Carbon::parse($schedule->departure_time)->format('H:i')) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800">
                    @error('departure_time')
                        <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_minutes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Duration (Minutes)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $schedule->duration_minutes) }}" placeholder="e.g. 300 (5 hrs)"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800">
                    @error('duration_minutes')
                        <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="arrival_time" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Est. Arrival Time</label>
                    <input type="time" name="arrival_time" id="arrival_time" value="{{ old('arrival_time', $schedule->arrival_time ? \Carbon\Carbon::parse($schedule->arrival_time)->format('H:i') : '') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800">
                    @error('arrival_time')
                        <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Status *</label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 bg-white">
                    <option value="active" {{ old('status', $schedule->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $schedule->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.schedules.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-lg shadow-red-900/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Update Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

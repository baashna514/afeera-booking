@extends('layouts.app', ['title' => 'Add Bus Vehicle - Super Admin'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-[#da1705]"></i>
                <span>Add New Bus Vehicle</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Register a bus vehicle with registration number and assigned service class.</p>
        </div>
        <a href="{{ route('admin.vehicles.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Back to Fleet
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.vehicles.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="bus_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Bus Number / Code *</label>
                    <input type="text" name="bus_number" id="bus_number" value="{{ old('bus_number') }}" placeholder="e.g. Bus B-102" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 placeholder-slate-400">
                    @error('bus_number')
                        <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="registration_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Registration Number / Plate *</label>
                    <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" placeholder="e.g. ABC-1234" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 placeholder-slate-400">
                    @error('registration_number')
                        <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="company_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Operating Bus Company *</label>
                <select name="company_id" id="company_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 bg-white">
                    <option value="">-- Select Company --</option>
                    @foreach($companies as $comp)
                        <option value="{{ $comp->id }}" {{ old('company_id') == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                    @endforeach
                </select>
                @error('company_id')
                    <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="vehicle_service_type_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Vehicle Service Class *</label>
                <select name="vehicle_service_type_id" id="vehicle_service_type_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 bg-white">
                    <option value="">-- Select Service Class --</option>
                    @foreach($vehicleTypes as $type)
                        <option value="{{ $type->id }}" {{ old('vehicle_service_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} ({{ $type->total_seats }} Seats)
                        </option>
                    @endforeach
                </select>
                @error('vehicle_service_type_id')
                    <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Vehicle Status *</label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#da1705] text-sm text-slate-800 bg-white">
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active (In Operation)</option>
                    <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-xs text-[#da1705] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.vehicles.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-lg shadow-red-900/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Save Bus Vehicle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

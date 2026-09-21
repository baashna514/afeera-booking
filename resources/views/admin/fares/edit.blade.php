@extends('layouts.app', ['title' => 'Edit Ticket Fare - Super Admin'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-rose-600"></i>
                <span>Edit Ticket Fare</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Update pricing between cities.</p>
        </div>
        <a href="{{ route('admin.fares.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Back to Fares
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.fares.update', $fare) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="route_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Route *</label>
                <select name="route_id" id="route_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800 bg-white">
                    <option value="">-- Select Route --</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id', $fare->route_id) == $route->id ? 'selected' : '' }}>
                            {{ $route->name }}
                        </option>
                    @endforeach
                </select>
                @error('route_id')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="from_city_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Boarding City (From) *</label>
                    <select name="from_city_id" id="from_city_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800 bg-white">
                        <option value="">-- Select From City --</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('from_city_id', $fare->from_city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('from_city_id')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="to_city_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Destination City (To) *</label>
                    <select name="to_city_id" id="to_city_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800 bg-white">
                        <option value="">-- Select To City --</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('to_city_id', $fare->to_city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('to_city_id')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="vehicle_service_type_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Vehicle Service Class *</label>
                <select name="vehicle_service_type_id" id="vehicle_service_type_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800 bg-white">
                    <option value="">-- Select Vehicle Class --</option>
                    @foreach($vehicleTypes as $type)
                        <option value="{{ $type->id }}" {{ old('vehicle_service_type_id', $fare->vehicle_service_type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_service_type_id')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fare_amount" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Fare Amount (PKR) *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs">Rs.</span>
                    <input type="number" step="10" name="fare_amount" id="fare_amount" value="{{ old('fare_amount', $fare->fare_amount) }}" required
                        class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm font-bold text-slate-800">
                </div>
                @error('fare_amount')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Status *</label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800 bg-white">
                    <option value="active" {{ old('status', $fare->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $fare->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.fares.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-rose-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Update Fare
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

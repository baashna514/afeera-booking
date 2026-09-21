@extends('layouts.app', ['title' => 'Edit Vehicle Service Type - Super Admin'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-rose-600"></i>
                <span>Edit Vehicle Service Type</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Update details for: {{ $vehicleServiceType->name }}</p>
        </div>
        <a href="{{ route('admin.vehicle-service-types.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Back to Types
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.vehicle-service-types.update', $vehicleServiceType) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Service Type Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $vehicleServiceType->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800">
                @error('name')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="total_seats" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Total Capacity (Seats) *</label>
                <input type="number" name="total_seats" id="total_seats" value="{{ old('total_seats', $vehicleServiceType->total_seats) }}" min="1" max="100" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800">
                @error('total_seats')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Description (Optional)</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800">{{ old('description', $vehicleServiceType->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Status *</label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800 bg-white">
                    <option value="active" {{ old('status', $vehicleServiceType->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $vehicleServiceType->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.vehicle-service-types.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-rose-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Update Service Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

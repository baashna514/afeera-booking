@extends('layouts.app', ['title' => 'Edit Route - Super Admin'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ 
    stops: {{ Js::from($route->stops->map(fn($s) => [
        'city_id' => (string)$s->city_id,
        'stop_order' => $s->stop_order,
        'distance_from_origin_km' => $s->distance_from_origin_km,
        'duration_from_origin_minutes' => $s->duration_from_origin_minutes
    ])) }} 
}">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-rose-600"></i>
                <span>Edit Route</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Update details for route: {{ $route->name }}</p>
        </div>
        <a href="{{ route('admin.routes.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Back to Routes
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.routes.update', $route) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Route Title / Description *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $route->name) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800">
                    @error('name')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="company_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Operating Bus Company *</label>
                    <select name="company_id" id="company_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800 bg-white">
                        <option value="">-- Select Company --</option>
                        @foreach($companies as $comp)
                            <option value="{{ $comp->id }}" {{ old('company_id', $route->company_id) == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="origin_city_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Origin City (Start) *</label>
                    <select name="origin_city_id" id="origin_city_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800 bg-white">
                        <option value="">-- Select Origin --</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('origin_city_id', $route->origin_city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('origin_city_id')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="destination_city_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Destination City (End) *</label>
                    <select name="destination_city_id" id="destination_city_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800 bg-white">
                        <option value="">-- Select Destination --</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('destination_city_id', $route->destination_city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('destination_city_id')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dynamic Intermediate Stops -->
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-[#da1705]"></i>
                            <span>Intermediate Stops / Cities (In Order)</span>
                        </h4>
                        <p class="text-xs text-slate-500">Add stations between origin and destination.</p>
                    </div>
                    <button type="button" @click="stops.push({ city_id: '', stop_order: stops.length + 1, distance_from_origin_km: '', duration_from_origin_minutes: '' })"
                        class="px-3 py-1.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1">
                        <i class="fa-solid fa-plus text-[10px]"></i> Add Stop
                    </button>
                </div>

                <template x-if="stops.length === 0">
                    <p class="text-xs text-slate-400 italic text-center py-3">No intermediate stops added yet.</p>
                </template>

                <div class="space-y-3">
                    <template x-for="(stop, index) in stops" :key="index">
                        <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">
                            <span class="w-6 h-6 rounded-full bg-red-50 text-[#da1705] flex items-center justify-center font-bold text-xs" x-text="index + 1"></span>
                            
                            <div class="flex-1">
                                <select :name="'stops[' + index + '][city_id]'" x-model="stop.city_id" required class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500">
                                    <option value="">-- Select Stop City --</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" :name="'stops[' + index + '][stop_order]'" :value="index + 1">
                            </div>

                            <div class="w-28">
                                <input type="number" step="0.1" :name="'stops[' + index + '][distance_from_origin_km]'" x-model="stop.distance_from_origin_km" placeholder="Dist (km)"
                                    class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500">
                            </div>

                            <div class="w-28">
                                <input type="number" :name="'stops[' + index + '][duration_from_origin_minutes]'" x-model="stop.duration_from_origin_minutes" placeholder="Time (mins)"
                                    class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500">
                            </div>

                            <button type="button" @click="stops.splice(index, 1)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Status *</label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm text-slate-800 bg-white">
                    <option value="active" {{ old('status', $route->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $route->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.routes.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-rose-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Update Route
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

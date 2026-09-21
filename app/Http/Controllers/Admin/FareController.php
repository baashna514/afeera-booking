<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Fare;
use App\Models\Route;
use App\Models\VehicleServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FareController extends Controller
{
    /**
     * Display a listing of fares.
     */
    public function index(): View
    {
        $fares = Fare::with(['route.originCity', 'route.destinationCity', 'fromCity', 'toCity', 'vehicleServiceType'])->latest()->get();

        return view('admin.fares.index', compact('fares'));
    }

    /**
     * Show the form for creating a new fare.
     */
    public function create(): View
    {
        $routes = Route::with(['originCity', 'destinationCity', 'stops.city'])->where('status', 'active')->get();
        $cities = City::where('status', 'active')->orderBy('name')->get();
        $vehicleTypes = VehicleServiceType::where('status', 'active')->get();

        return view('admin.fares.create', compact('routes', 'cities', 'vehicleTypes'));
    }

    /**
     * Store a newly created fare.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'from_city_id' => 'required|exists:cities,id',
            'to_city_id' => 'required|exists:cities,id|different:from_city_id',
            'vehicle_service_type_id' => 'required|exists:vehicle_service_types,id',
            'fare_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        Fare::create($validated);

        return redirect()->route('admin.fares.index')
            ->with('success', 'Fare created successfully.');
    }

    /**
     * Show the form for editing a fare.
     */
    public function edit(Fare $fare): View
    {
        $routes = Route::with(['originCity', 'destinationCity', 'stops.city'])->where('status', 'active')->get();
        $cities = City::where('status', 'active')->orderBy('name')->get();
        $vehicleTypes = VehicleServiceType::where('status', 'active')->get();

        return view('admin.fares.edit', compact('fare', 'routes', 'cities', 'vehicleTypes'));
    }

    /**
     * Update the specified fare.
     */
    public function update(Request $request, Fare $fare): RedirectResponse
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'from_city_id' => 'required|exists:cities,id',
            'to_city_id' => 'required|exists:cities,id|different:from_city_id',
            'vehicle_service_type_id' => 'required|exists:vehicle_service_types,id',
            'fare_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $fare->update($validated);

        return redirect()->route('admin.fares.index')
            ->with('success', 'Fare updated successfully.');
    }

    /**
     * Remove the specified fare.
     */
    public function destroy(Fare $fare): RedirectResponse
    {
        $fare->delete();

        return redirect()->route('admin.fares.index')
            ->with('success', 'Fare deleted successfully.');
    }
}

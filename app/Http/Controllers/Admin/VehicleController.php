<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\VehicleServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /**
     * Display a listing of bus vehicles.
     */
    public function index(): View
    {
        $vehicles = Vehicle::with(['company', 'vehicleServiceType'])->latest()->get();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new bus vehicle.
     */
    public function create(): View
    {
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $vehicleTypes = VehicleServiceType::where('status', 'active')->orderBy('name')->get();

        return view('admin.vehicles.create', compact('companies', 'vehicleTypes'));
    }

    /**
     * Store a newly created bus vehicle.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bus_number' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'vehicle_service_type_id' => 'required|exists:vehicle_service_types,id',
            'status' => 'required|in:active,maintenance,inactive',
        ]);

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Bus vehicle added successfully.');
    }

    /**
     * Show the form for editing a bus vehicle.
     */
    public function edit(Vehicle $vehicle): View
    {
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $vehicleTypes = VehicleServiceType::where('status', 'active')->orderBy('name')->get();

        return view('admin.vehicles.edit', compact('vehicle', 'companies', 'vehicleTypes'));
    }

    /**
     * Update the specified bus vehicle.
     */
    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'bus_number' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'vehicle_service_type_id' => 'required|exists:vehicle_service_types,id',
            'status' => 'required|in:active,maintenance,inactive',
        ]);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Bus vehicle details updated successfully.');
    }

    /**
     * Remove the specified bus vehicle.
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Bus vehicle deleted successfully.');
    }
}

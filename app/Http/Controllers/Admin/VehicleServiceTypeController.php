<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleServiceTypeController extends Controller
{
    /**
     * Display a listing of vehicle service types.
     */
    public function index(): View
    {
        $vehicleTypes = VehicleServiceType::latest()->get();

        return view('admin.vehicle-service-types.index', compact('vehicleTypes'));
    }

    /**
     * Show the form for creating a new vehicle service type.
     */
    public function create(): View
    {
        return view('admin.vehicle-service-types.create');
    }

    /**
     * Store a newly created vehicle service type.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_seats' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ]);

        VehicleServiceType::create($validated);

        return redirect()->route('admin.vehicle-service-types.index')
            ->with('success', 'Vehicle Service Type created successfully.');
    }

    /**
     * Show the form for editing a vehicle service type.
     */
    public function edit(VehicleServiceType $vehicleServiceType): View
    {
        return view('admin.vehicle-service-types.edit', compact('vehicleServiceType'));
    }

    /**
     * Update the specified vehicle service type.
     */
    public function update(Request $request, VehicleServiceType $vehicleServiceType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_seats' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ]);

        $vehicleServiceType->update($validated);

        return redirect()->route('admin.vehicle-service-types.index')
            ->with('success', 'Vehicle Service Type updated successfully.');
    }

    /**
     * Remove the specified vehicle service type.
     */
    public function destroy(VehicleServiceType $vehicleServiceType): RedirectResponse
    {
        $vehicleServiceType->delete();

        return redirect()->route('admin.vehicle-service-types.index')
            ->with('success', 'Vehicle Service Type deleted successfully.');
    }
}

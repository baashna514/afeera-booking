<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Vehicle;
use App\Models\VehicleServiceType;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     */
    public function index(): View
    {
        $schedules = Schedule::with(['route.originCity', 'route.destinationCity', 'vehicleServiceType', 'vehicle'])->latest()->get();

        return view('admin.schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create(): View
    {
        $routes = Route::with(['originCity', 'destinationCity'])->where('status', 'active')->get();
        $vehicleTypes = VehicleServiceType::where('status', 'active')->get();
        $vehicles = Vehicle::with(['company', 'vehicleServiceType'])->where('status', 'active')->get();

        return view('admin.schedules.create', compact('routes', 'vehicleTypes', 'vehicles'));
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_service_type_id' => 'required|exists:vehicle_service_types,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'departure_time' => 'required|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:1',
            'arrival_time' => 'nullable|date_format:H:i',
            'status' => 'required|in:active,inactive',
        ]);

        // Auto-calculate arrival time if duration_minutes is given
        if (! empty($validated['duration_minutes']) && empty($validated['arrival_time'])) {
            $departure = Carbon::createFromFormat('H:i', $validated['departure_time']);
            $validated['arrival_time'] = $departure->addMinutes((int) $validated['duration_minutes'])->format('H:i');
        }

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule created successfully.');
    }

    /**
     * Show the form for editing a schedule.
     */
    public function edit(Schedule $schedule): View
    {
        $routes = Route::with(['originCity', 'destinationCity'])->where('status', 'active')->get();
        $vehicleTypes = VehicleServiceType::where('status', 'active')->get();
        $vehicles = Vehicle::with(['company', 'vehicleServiceType'])->where('status', 'active')->get();

        return view('admin.schedules.edit', compact('schedule', 'routes', 'vehicleTypes', 'vehicles'));
    }

    /**
     * Update the specified schedule.
     */
    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_service_type_id' => 'required|exists:vehicle_service_types,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'departure_time' => 'required|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:1',
            'arrival_time' => 'nullable|date_format:H:i',
            'status' => 'required|in:active,inactive',
        ]);

        // Auto-calculate arrival time if duration_minutes is given
        if (! empty($validated['duration_minutes']) && empty($validated['arrival_time'])) {
            $departure = Carbon::createFromFormat('H:i', $validated['departure_time']);
            $validated['arrival_time'] = $departure->addMinutes((int) $validated['duration_minutes'])->format('H:i');
        }

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Company;
use App\Models\Route;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RouteController extends Controller
{
    /**
     * Display a listing of routes.
     */
    public function index(): View
    {
        $routes = Route::with(['originCity', 'destinationCity', 'company', 'stops'])->latest()->get();

        return view('admin.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new route.
     */
    public function create(): View
    {
        $cities = City::where('status', 'active')->orderBy('name')->get();
        $companies = Company::where('status', 'active')->orderBy('name')->get();

        return view('admin.routes.create', compact('cities', 'companies'));
    }

    /**
     * Store a newly created route.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'origin_city_id' => 'required|exists:cities,id',
            'destination_city_id' => 'required|exists:cities,id|different:origin_city_id',
            'status' => 'required|in:active,inactive',
            'stops' => 'nullable|array',
            'stops.*.city_id' => 'required_with:stops|exists:cities,id',
            'stops.*.stop_order' => 'required_with:stops|integer|min:1',
            'stops.*.distance_from_origin_km' => 'nullable|numeric|min:0',
            'stops.*.duration_from_origin_minutes' => 'nullable|integer|min:0',
        ]);

        $route = Route::create([
            'name' => $validated['name'],
            'company_id' => $validated['company_id'],
            'origin_city_id' => $validated['origin_city_id'],
            'destination_city_id' => $validated['destination_city_id'],
            'status' => $validated['status'],
        ]);

        if (! empty($validated['stops'])) {
            foreach ($validated['stops'] as $stop) {
                $route->stops()->create($stop);
            }
        }

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route created successfully.');
    }

    /**
     * Show the form for editing a route.
     */
    public function edit(Route $route): View
    {
        $route->load('stops.city');
        $cities = City::where('status', 'active')->orderBy('name')->get();
        $companies = Company::where('status', 'active')->orderBy('name')->get();

        return view('admin.routes.edit', compact('route', 'cities', 'companies'));
    }

    /**
     * Update the specified route.
     */
    public function update(Request $request, Route $route): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'origin_city_id' => 'required|exists:cities,id',
            'destination_city_id' => 'required|exists:cities,id|different:origin_city_id',
            'status' => 'required|in:active,inactive',
            'stops' => 'nullable|array',
            'stops.*.city_id' => 'required_with:stops|exists:cities,id',
            'stops.*.stop_order' => 'required_with:stops|integer|min:1',
            'stops.*.distance_from_origin_km' => 'nullable|numeric|min:0',
            'stops.*.duration_from_origin_minutes' => 'nullable|integer|min:0',
        ]);

        $route->update([
            'name' => $validated['name'],
            'company_id' => $validated['company_id'],
            'origin_city_id' => $validated['origin_city_id'],
            'destination_city_id' => $validated['destination_city_id'],
            'status' => $validated['status'],
        ]);

        // Replace stops
        $route->stops()->delete();
        if (! empty($validated['stops'])) {
            foreach ($validated['stops'] as $stop) {
                $route->stops()->create($stop);
            }
        }

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route updated successfully.');
    }

    /**
     * Remove the specified route.
     */
    public function destroy(Route $route): RedirectResponse
    {
        $route->delete();

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    /**
     * Display a listing of cities.
     */
    public function index(): View
    {
        $cities = City::latest()->get();

        return view('admin.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new city.
     */
    public function create(): View
    {
        return view('admin.cities.create');
    }

    /**
     * Store a newly created city.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'province' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        City::create($validated);

        return redirect()->route('admin.cities.index')
            ->with('success', 'City created successfully.');
    }

    /**
     * Show the form for editing a city.
     */
    public function edit(City $city): View
    {
        return view('admin.cities.edit', compact('city'));
    }

    /**
     * Update the specified city.
     */
    public function update(Request $request, City $city): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'province' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $city->update($validated);

        return redirect()->route('admin.cities.index')
            ->with('success', 'City updated successfully.');
    }

    /**
     * Remove the specified city.
     */
    public function destroy(City $city): RedirectResponse
    {
        $city->delete();

        return redirect()->route('admin.cities.index')
            ->with('success', 'City deleted successfully.');
    }
}

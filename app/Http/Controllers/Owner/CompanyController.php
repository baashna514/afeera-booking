<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Companies ki list dikhana
    public function index()
    {
        // Sirf logged-in owner ki companies fetch karein
        $companies = Company::where('owner_id', auth()->id())->latest()->get();

        return view('owner.companies.index', compact('companies'));
    }

    // Company add karne ka form dikhana
    public function create()
    {
        return view('owner.companies.create');
    }

    // Company database mein save karna
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        Company::create([
            'name' => $request->name,
            'code' => $request->code,
            'address' => $request->address,
            'phone' => $request->phone,
            'owner_id' => auth()->id(),
            'status' => 'active',
        ]);

        return redirect()->route('owner.companies.index')->with('success', 'Company created successfully.');
    }

    // Company edit karne ka form dikhana
    public function edit(Company $company)
    {
        if ($company->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('owner.companies.edit', compact('company'));
    }

    // Company update karna
    public function update(Request $request, Company $company)
    {
        if ($company->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $company->update($request->only(['name', 'code', 'address', 'phone', 'status']));

        return redirect()->route('owner.companies.index')->with('success', 'Company updated successfully.');
    }

    // Company delete karna
    public function destroy(Company $company)
    {
        if ($company->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $company->delete();

        return redirect()->route('owner.companies.index')->with('success', 'Company deleted successfully.');
    }
}

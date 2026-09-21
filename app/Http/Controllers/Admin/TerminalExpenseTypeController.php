<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TerminalExpenseType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TerminalExpenseTypeController extends Controller
{
    /**
     * Display a listing of terminal expense types.
     */
    public function index(Request $request): View
    {
        $query = TerminalExpenseType::latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $expenseTypes = $query->paginate(15)->withQueryString();

        return view('admin.expense_types.index', compact('expenseTypes'));
    }

    /**
     * Show the form for creating a new terminal expense type.
     */
    public function create(): View
    {
        return view('admin.expense_types.create');
    }

    /**
     * Store a newly created terminal expense type in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:terminal_expense_types,name',
            'status' => 'required|in:active,inactive',
        ]);

        TerminalExpenseType::create($validated);

        return redirect()->route('admin.expense-types.index')
            ->with('success', 'Terminal Expense Type created successfully.');
    }

    /**
     * Show the form for editing the specified terminal expense type.
     */
    public function edit(TerminalExpenseType $expenseType): View
    {
        return view('admin.expense_types.edit', compact('expenseType'));
    }

    /**
     * Update the specified terminal expense type in storage.
     */
    public function update(Request $request, TerminalExpenseType $expenseType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:terminal_expense_types,name,'.$expenseType->id,
            'status' => 'required|in:active,inactive',
        ]);

        $expenseType->update($validated);

        return redirect()->route('admin.expense-types.index')
            ->with('success', 'Terminal Expense Type updated successfully.');
    }

    /**
     * Remove the specified terminal expense type from storage.
     */
    public function destroy(TerminalExpenseType $expenseType): RedirectResponse
    {
        $expenseType->delete();

        return redirect()->route('admin.expense-types.index')
            ->with('success', 'Terminal Expense Type deleted successfully.');
    }
}

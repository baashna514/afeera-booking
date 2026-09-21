@extends('layouts.app', ['title' => 'Edit Terminal Expense Type - Super Admin'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-rose-600"></i>
                <span>Edit Terminal Expense Type</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Update expense title or status.</p>
        </div>
        <a href="{{ route('admin.expense-types.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.expense-types.update', $expenseType) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Expense Title / Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $expenseType->name) }}" required
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 text-sm font-medium text-slate-900 outline-none transition @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Status <span class="text-rose-500">*</span>
                </label>
                <select name="status" id="status" required
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 text-sm font-medium text-slate-900 outline-none transition">
                    <option value="active" {{ old('status', $expenseType->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $expenseType->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('admin.expense-types.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-rose-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Update Expense Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

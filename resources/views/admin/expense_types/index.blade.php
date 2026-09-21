@extends('layouts.app', ['title' => 'Manage Terminal Expense Types - Super Admin'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-rose-600"></i>
                <span>Terminal Expense Types</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Manage master list of terminal expense titles (Bus Stand Fee, Cleaning, IT Fee, etc.) for departure vouchers.</p>
        </div>
        <div>
            <a href="{{ route('admin.expense-types.create') }}" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-rose-600/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add Expense Type
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        @if($expenseTypes->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">No Expense Types Found</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Click 'Add Expense Type' above to create the first expense title.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">#</th>
                            <th class="px-4 py-3">Expense Title / Category</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($expenseTypes as $type)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono text-xs text-slate-400">#{{ $type->id }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $type->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $type->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ $type->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.expense-types.edit', $type) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.expense-types.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this expense type?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-lg transition inline-flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $expenseTypes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
